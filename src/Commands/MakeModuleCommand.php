<?php

namespace DanielAlva\Modular\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module {name : The name of the module (e.g. Products)}';
    protected $description = 'Create a new modular architecture module (by Daniel Alva)';

    public function handle(): int
    {
        $rawName = $this->argument('name');
        $module = Str::studly($rawName);
        $modulesDir = base_path(config('modular.modules_directory', 'modules'));
        $targetPath = $modulesDir . '/' . $module;

        if (File::isDirectory($targetPath)) {
            $this->error("❌ Module [{$module}] already exists at: {$targetPath}");
            return Command::FAILURE;
        }

        $this->info("🚀 Generating module [{$module}] by Daniel Alva...");

        // 1. Create directory structure
        $folders = ['Controllers', 'Models', 'Migrations', 'Routes', 'Views', 'Requests'];
        foreach ($folders as $folder) {
            File::makeDirectory($targetPath . '/' . $folder, 0755, true);
        }

        $namespace = config('modular.modules_namespace', 'Modules') . '\\' . $module;
        $viewNamespace = strtolower($module);
        $routePrefix = Str::kebab($module);

        // 2. Generate Controller
        $controllerTemplate = File::get(__DIR__ . '/../Stubs/controller.stub');
        $controllerContent = str_replace(
            ['{{namespace}}', '{{class}}', '{{viewNamespace}}'],
            [$namespace, $module, $viewNamespace],
            $controllerTemplate
        );
        File::put($targetPath . "/Controllers/{$module}Controller.php", $controllerContent);

        // 3. Generate Model
        $modelTemplate = File::get(__DIR__ . '/../Stubs/model.stub');
        $modelContent = str_replace(
            ['{{namespace}}', '{{class}}'],
            [$namespace, $module],
            $modelTemplate
        );
        File::put($targetPath . "/Models/{$module}.php", $modelContent);

        // 4. Generate Migration
        $tableName = Str::snake(Str::pluralStudly($module));
        $timestamp = date('Y_m_d_His');
        $migrationContent = "<?php\n\nuse Illuminate\\Database\\Migrations\\Migration;\nuse Illuminate\\Database\\Schema\\Blueprint;\nuse Illuminate\\Support\\Facades\\Schema;\n\nreturn new class extends Migration\n{\n    public function up(): void\n    {\n        Schema::create('{$tableName}', function (Blueprint \$table) {\n            \$table->id();\n            \$table->string('name')->nullable();\n            \$table->timestamps();\n        });\n    }\n\n    public function down(): void\n    {\n        Schema::dropIfExists('{$tableName}');\n    }\n};\n";
        File::put($targetPath . "/Migrations/{$timestamp}_create_{$tableName}_table.php", $migrationContent);

        // 5. Generate Web Routes
        $routesWebTemplate = File::get(__DIR__ . '/../Stubs/routes-web.stub');
        $routesWebContent = str_replace(
            ['{{namespace}}', '{{class}}', '{{module}}', '{{routePrefix}}'],
            [$namespace, $module, $module, $routePrefix],
            $routesWebTemplate
        );
        File::put($targetPath . '/Routes/web.php', $routesWebContent);

        // 6. Generate API Routes
        $routesApiTemplate = File::get(__DIR__ . '/../Stubs/routes-api.stub');
        $routesApiContent = str_replace(
            ['{{namespace}}', '{{class}}', '{{module}}', '{{routePrefix}}'],
            [$namespace, $module, $module, $routePrefix],
            $routesApiTemplate
        );
        File::put($targetPath . '/Routes/api.php', $routesApiContent);

        // 7. Generate Blade View
        $viewTemplate = File::get(__DIR__ . '/../Stubs/view.stub');
        $viewContent = str_replace('{{module}}', $module, $viewTemplate);
        File::put($targetPath . '/Views/index.blade.php', $viewContent);

        $this->info("✅ Module [{$module}] successfully created at: modules/{$module}");
        $this->line("👉 Web route ready at: /{$routePrefix}");
        $this->line("👉 API route ready at: /api/{$routePrefix}");
        $this->line("👉 Blade view ready: view('{$viewNamespace}::index')");

        return Command::SUCCESS;
    }
}
