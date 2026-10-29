<?php

namespace DanielAlva\Modular\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeControllerCommand extends Command
{
    protected $signature = 'module:controller {module : Nombre del módulo} {name : Nombre del controlador}';
    protected $description = 'Crea un controlador dentro de un módulo específico';

    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $rawName = Str::studly($this->argument('name'));
        $controllerName = Str::endsWith($rawName, 'Controller') ? $rawName : $rawName . 'Controller';

        $modulesDir = base_path(config('modular.modules_directory', 'modules'));
        $targetDir = $modulesDir . '/' . $module . '/Controllers';

        if (!File::isDirectory($modulesDir . '/' . $module)) {
            $this->error("❌ El módulo [{$module}] no existe.");
            return Command::FAILURE;
        }

        File::ensureDirectoryExists($targetDir);
        $filePath = $targetDir . '/' . $controllerName . '.php';

        if (File::exists($filePath)) {
            $this->error("❌ El controlador [{$controllerName}] ya existe en el módulo [{$module}].");
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
        $this->info("✅ Controlador [{$controllerName}] creado en modules/{$module}/Controllers/");
        return Command::SUCCESS;
    }
}
