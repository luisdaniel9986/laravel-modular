<?php

namespace DanielAlva\Modular;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use DanielAlva\Modular\Commands\MakeModuleCommand;
use DanielAlva\Modular\Commands\MakeControllerCommand;
use DanielAlva\Modular\Commands\MakeModelCommand;
use DanielAlva\Modular\Commands\MakeMigrationCommand;

class ModularServiceProvider extends ServiceProvider
{
    /**
     * Registrar servicios y configuración del paquete.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/modular.php', 'modular');
    }

    /**
     * Inicializar servicios y componentes de los módulos.
     */
    public function boot(): void
    {
        // Publicar configuración
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/Config/modular.php' => config_path('modular.php'),
            ], 'modular-config');

            // Registrar comandos Artisan
            $this->commands([
                MakeModuleCommand::class,
                MakeControllerCommand::class,
                MakeModelCommand::class,
                MakeMigrationCommand::class,
            ]);
        }

        // Descubrir y cargar todos los módulos existentes
        $this->loadModules();
    }

    /**
     * Escanear el directorio de módulos y registrar rutas, vistas y migraciones.
     */
    protected function loadModules(): void
    {
        $modulesDir = base_path(config('modular.modules_directory', 'modules'));

        if (!File::isDirectory($modulesDir)) {
            return;
        }

        $modules = File::directories($modulesDir);

        foreach ($modules as $modulePath) {
            $moduleName = basename($modulePath);
            $this->bootModule($moduleName, $modulePath);
        }
    }

    /**
     * Cargar los componentes individuales de un módulo específico.
     */
    protected function bootModule(string $moduleName, string $modulePath): void
    {
        $autoLoad = config('modular.auto_load', [
            'routes'     => true,
            'migrations' => true,
            'views'      => true,
        ]);

        // 1. Cargar Rutas (Web y API)
        if ($autoLoad['routes'] ?? true) {
            $webRoutes = $modulePath . '/Routes/web.php';
            if (File::exists($webRoutes)) {
                Route::middleware('web')
                    ->group($webRoutes);
            }

            $apiRoutes = $modulePath . '/Routes/api.php';
            if (File::exists($apiRoutes)) {
                Route::middleware('api')
                    ->prefix('api')
                    ->group($apiRoutes);
            }
        }

        // 2. Cargar Migraciones
        if ($autoLoad['migrations'] ?? true) {
            $migrationsPath = $modulePath . '/Migrations';
            if (File::isDirectory($migrationsPath)) {
                $this->loadMigrationsFrom($migrationsPath);
            }
        }

        // 3. Cargar Vistas Blade (ejemplo: view('productos::index'))
        if ($autoLoad['views'] ?? true) {
            $viewsPath = $modulePath . '/Views';
            if (File::isDirectory($viewsPath)) {
                $this->loadViewsFrom($viewsPath, strtolower($moduleName));
            }
        }
    }
}
