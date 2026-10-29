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
     * Register services and package configuration.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/modular.php', 'modular');
    }

    /**
     * Bootstrap package services, commands and auto-discover modules.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/Config/modular.php' => config_path('modular.php'),
            ], 'modular-config');

            $this->commands([
                MakeModuleCommand::class,
                MakeControllerCommand::class,
                MakeModelCommand::class,
                MakeMigrationCommand::class,
            ]);
        }

        $this->loadModules();
    }

    /**
     * Scan the modules directory and register routes, views, and migrations.
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
     * Boot individual module components.
     */
    protected function bootModule(string $moduleName, string $modulePath): void
    {
        $autoLoad = config('modular.auto_load', [
            'routes'     => true,
            'migrations' => true,
            'views'      => true,
        ]);

        // 1. Load Routes (Web & API)
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

        // 2. Load Migrations
        if ($autoLoad['migrations'] ?? true) {
            $migrationsPath = $modulePath . '/Migrations';
            if (File::isDirectory($migrationsPath)) {
                $this->loadMigrationsFrom($migrationsPath);
            }
        }

        // 3. Load Blade Views
        if ($autoLoad['views'] ?? true) {
            $viewsPath = $modulePath . '/Views';
            if (File::isDirectory($viewsPath)) {
                $this->loadViewsFrom($viewsPath, strtolower($moduleName));
            }
        }
    }
}
