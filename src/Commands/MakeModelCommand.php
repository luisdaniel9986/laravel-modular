<?php

namespace DanielAlva\Modular\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModelCommand extends Command
{
    protected $signature = 'module:model {module : Nombre del módulo} {name : Nombre del modelo} {--m|migration : Crear también la migración}';
    protected $description = 'Crea un modelo dentro de un módulo específico';

    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $modelName = Str::studly($this->argument('name'));

        $modulesDir = base_path(config('modular.modules_directory', 'modules'));
        $targetDir = $modulesDir . '/' . $module . '/Models';

        if (!File::isDirectory($modulesDir . '/' . $module)) {
            $this->error("❌ El módulo [{$module}] no existe.");
            return Command::FAILURE;
        }

        File::ensureDirectoryExists($targetDir);
        $filePath = $targetDir . '/' . $modelName . '.php';

        if (File::exists($filePath)) {
            $this->error("❌ El modelo [{$modelName}] ya existe en el módulo [{$module}].");
            return Command::FAILURE;
        }

        $namespace = config('modular.modules_namespace', 'Modules') . '\\' . $module;
        $template = File::get(__DIR__ . '/../Stubs/model.stub');
        $content = str_replace(
            ['{{namespace}}', '{{class}}'],
            [$namespace, $modelName],
            $template
        );

        File::put($filePath, $content);
        $this->info("✅ Modelo [{$modelName}] creado en modules/{$module}/Models/");

        if ($this->option('migration')) {
            $this->call('module:migration', [
                'module' => $module,
                'name' => "create_" . Str::snake(Str::pluralStudly($modelName)) . "_table"
            ]);
        }

        return Command::SUCCESS;
    }
}
