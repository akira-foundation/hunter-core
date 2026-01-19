<?php

declare(strict_types=1);

namespace Hunter\Module\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Env;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

final class ServeCommand extends Command
{
    public $signature = 'hunter:serve
        {--host=127.0.0.1 : The host address to serve on}
        {--port=8088 : The port to serve on}
        {--no-reload : Do not reload the server on .env file changes}';

    public $description = 'Start the Hunter workbench development server';

    public function handle(): int
    {
        if (! $this->isWorkbenchEnvironment()) {
            warning('This command should only be run in a module workbench environment.');

            return self::FAILURE;
        }

        $host = (string) $this->option('host');
        $port = (string) $this->option('port');

        info("Starting Hunter workbench server on http://{$host}:{$port}");
        $this->newLine();

        $process = $this->startProcess($host, $port);

        while ($process->isRunning()) {
            if (! $this->option('no-reload') && $this->envFileChanged()) {
                $process->stop();
                $process = $this->startProcess($host, $port);
            }

            usleep(500_000);
        }

        return $process->getExitCode() ?? self::SUCCESS;
    }

    private function isWorkbenchEnvironment(): bool
    {
        // Check if running within Orchestra Testbench
        return str_contains(base_path(), 'testbench-core');
    }

    private function startProcess(string $host, string $port): Process
    {
        $phpBinary = (new PhpExecutableFinder())->find(false) ?: 'php';

        $serverPath = base_path('vendor/orchestra/testbench-core/laravel/server.php');
        $publicPath = base_path('vendor/orchestra/testbench-core/laravel/public');

        $process = new Process([
            $phpBinary,
            '-S', "{$host}:{$port}",
            $serverPath,
        ], $publicPath, collect($_ENV)->mapWithKeys(fn ($value, $key) => [$key => $value])->merge([
            'APP_ENV' => Env::get('APP_ENV', 'local'),
        ])->all());

        $process->setTimeout(null);
        $process->start(function ($type, $buffer): void {
            $this->output->write($buffer);
        });

        return $process;
    }

    private function envFileChanged(): bool
    {
        static $lastModified = null;

        $envPath = base_path('.env');

        if (! file_exists($envPath)) {
            return false;
        }

        $currentModified = filemtime($envPath);

        if ($lastModified === null) {
            $lastModified = $currentModified;

            return false;
        }

        if ($currentModified !== $lastModified) {
            $lastModified = $currentModified;

            return true;
        }

        return false;
    }
}
