<?php

namespace DanielAlva\Modular\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module {name : Nombre del módulo (ej. Productos)}';
    protected $description = 'Crea un nuevo módulo con arquitectura modular completa (Daniel Alva)';

    public function handle(): int
    {
        $rawName = $this->argument('name');
        $module = Str::studly($rawName);
        $modulesDir = base_path(config('modular.modules_directory', 'modules'));
        $targetPath = $modulesDir . '/' . $module;

        if (File::isDirectory($targetPath)) {
            $this->error("❌ El módulo [{$module}] ya existe en: {$targetPath}");
            return Command::FAILURE;
        }

        $this->info("🚀 Creando módulo modular [{$module}] por Daniel Alva...");

        // 1. Crear directorios
        $folders = ['Controllers', 'Models', 'Migrations', 'Routes', 'Views', 'Requests'];
        foreach ($folders as $folder) {
            File::makeDirectory($targetPath . '/' . $folder, 0755, true);
        }

        $namespace = config('modular.modules_namespace', 'Modules') . '\\' . $module;
        $viewNamespace = strtolower($module);
        $routePrefix = Str::kebab($module);

        // 2. Generar Controller
        $controllerTemplate = File::get(__DIR__ . '/../Stubs/controller.stub');
        $controllerContent = str_replace(
            ['{{namespace}}', '{{class}}', '{{viewNamespace}}'],
            [$namespace, $module, $viewNamespace],
            $controllerTemplate
        );
        File::put($targetPath . "/Controllers/{$module}Controller.php", $controllerContent);

        // 3. Generar Model
        $modelTemplate = File::get(__DIR__ . '/../Stubs/model.stub');
        $modelContent = str_replace(
            ['{{namespace}}', '{{class}}'],
            [$namespace, $module],
            $modelTemplate
        );
        File::put($targetPath . "/Models/{$module}.php", $modelContent);

        // 4. Generar Migración
        $tableName = Str::snake(Str::pluralStudly($module));
        $timestamp = date('Y_m_d_His');
        $migrationContent = "<?php\n\nuse Illuminate\\Database\\Migrations\\Migration;\nuse Illuminate\\Database\\Schema\\Blueprint;\nuse Illuminate\\Support\\Facades\\Schema;\n\nreturn new class extends Migration\n{\n    public function up(): void\n    {\n        Schema::create('{$tableName}', function (Blueprint \$table) {\n            \$table->id();\n            \$table->string('nombre')->nullable();\n            \$table->timestamps();\n        });\n    }\n\n    public function down(): void\n    {\n        Schema::dropIfExists('{$tableName}');\n    }\n};\n";
        File::put($targetPath . "/Migrations/{$timestamp}_create_{$tableName}_table.php", $migrationContent);

        // 5. Generar Rutas Web
        $routesWebTemplate = File::get(__DIR__ . '/../Stubs/routes-web.stub');
        $routesWebContent = str_replace(
            ['{{namespace}}', '{{class}}', '{{module}}', '{{routePrefix}}'],
            [$namespace, $module, $module, $routePrefix],
            $routesWebTemplate
        );
        File::put($targetPath . '/Routes/web.php', $routesWebContent);

        // 6. Generar Rutas API
        $routesApiTemplate = File::get(__DIR__ . '/../Stubs/routes-api.stub');
        $routesApiContent = str_replace(
            ['{{namespace}}', '{{class}}', '{{module}}', '{{routePrefix}}'],
            [$namespace, $module, $module, $routePrefix],
            $routesApiTemplate
        );
        File::put($targetPath . '/Routes/api.php', $routesApiContent);

        // 7. Generar Vista Blade inicial
        $viewTemplate = File::get(__DIR__ . '/../Stubs/view.stub');
        $viewContent = str_replace('{{module}}', $module, $viewTemplate);
        File::put($targetPath . '/Views/index.blade.php', $viewContent);

        $this->info("✅ Módulo [{$module}] creado exitosamente en: modules/{$module}");
        $this->line("👉 Rutas web disponibles en: /{$routePrefix}");
        $this->line("👉 Rutas API disponibles en: /api/{$routePrefix}");
        $this->line("👉 Vista lista para usar: view('{$viewNamespace}::index')");

        return Command::SUCCESS;
    }
}
