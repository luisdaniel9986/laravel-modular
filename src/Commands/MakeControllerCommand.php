<?php

namespace DanielAlva\Modular\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeControllerCommand extends Command
{
    protected $signature = 'module:controller {module : Module name} {name : Controller name}';
    protected $description = 'Create a new controller inside a specific module';

    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $rawName = Str::studly($this->argument('name'));
        $controllerName = Str::endsWith($rawName, 'Controller') ? $rawName : $rawName . 'Controller';

        $modulesDir = base_path(config('modular.modules_directory', 'modules'));
        $targetDir = $modulesDir . '/' . $module . '/Controllers';

        if (!File::isDirectory($modulesDir . '/' . $module)) {
            $this->error("❌ Module [{$module}] does not exist.");
            return Command::FAILURE;
        }

        File::ensureDirectoryExists($targetDir);
        $filePath = $targetDir . '/' . $controllerName . '.php';

        if (File::exists($filePath)) {
            $this->error("❌ Controller [{$controllerName}] already exists in module [{$module}].");
            return Command::FAILURE;
        }

        $namespace = config('modular.modules_namespace', 'Modules') . '\\' . $module;
        $viewNamespace = strtolower($module);

        $template = File::get(__DIR__ . '/../Stubs/controller.stub');
        $content = str_replace(
            ['{{namespace}}', '{{class}}', '{{viewNamespace}}'],
            [$namespace, str_replace('Controller', '', $controllerName), $viewNamespace],
            $template
        );

        File::put($filePath, $content);
        $this->info("✅ Controller [{$controllerName}] created in modules/{$module}/Controllers/");
        return Command::SUCCESS;
    }
}
