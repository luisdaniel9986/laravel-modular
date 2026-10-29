<?php

namespace DanielAlva\Modular\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModelCommand extends Command
{
    protected $signature = 'module:model {module : Module name} {name : Model name} {--m|migration : Create a new migration file for the model}';
    protected $description = 'Create a new model inside a specific module';

    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $modelName = Str::studly($this->argument('name'));

        $modulesDir = base_path(config('modular.modules_directory', 'modules'));
        $targetDir = $modulesDir . '/' . $module . '/Models';

        if (!File::isDirectory($modulesDir . '/' . $module)) {
            $this->error("❌ Module [{$module}] does not exist.");
            return Command::FAILURE;
        }

        File::ensureDirectoryExists($targetDir);
        $filePath = $targetDir . '/' . $modelName . '.php';

        if (File::exists($filePath)) {
            $this->error("❌ Model [{$modelName}] already exists in module [{$module}].");
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
        $this->info("✅ Model [{$modelName}] created in modules/{$module}/Models/");

        if ($this->option('migration')) {
            $this->call('module:migration', [
                'module' => $module,
                'name' => "create_" . Str::snake(Str::pluralStudly($modelName)) . "_table"
            ]);
        }

        return Command::SUCCESS;
    }
}
