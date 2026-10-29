<?php

namespace DanielAlva\Modular\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeMigrationCommand extends Command
{
    protected $signature = 'module:migration {module : Nombre del módulo} {name : Nombre de la migración}';
    protected $description = 'Crea una migración dentro de un módulo específico';

    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $rawName = Str::snake($this->argument('name'));

        $modulesDir = base_path(config('modular.modules_directory', 'modules'));
        $targetDir = $modulesDir . '/' . $module . '/Migrations';

        if (!File::isDirectory($modulesDir . '/' . $module)) {
            $this->error("❌ El módulo [{$module}] no existe.");
            return Command::FAILURE;
        }

        File::ensureDirectoryExists($targetDir);
        $timestamp = date('Y_m_d_His');
        $fileName = "{$timestamp}_{$rawName}.php";
        $filePath = $targetDir . '/' . $fileName;

        $content = "<?php\n\nuse Illuminate\\Database\\Migrations\\Migration;\nuse Illuminate\\Database\\Schema\\Blueprint;\nuse Illuminate\\Support\\Facades\\Schema;\n\nreturn new class extends Migration\n{\n    public function up(): void\n    {\n        // Define tu tabla o cambios aquí\n    }\n\n    public function down(): void\n    {\n        // Revertir cambios\n    }\n};\n";

        File::put($filePath, $content);
        $this->info("✅ Migración creada en modules/{$module}/Migrations/{$fileName}");
        return Command::SUCCESS;
    }
}
