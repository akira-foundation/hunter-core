<?php

declare(strict_types=1);

namespace Hunter\Core\Tests;

use Akira\Debugger\DebuggerServiceProvider;
use Hunter\Core\CoreServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Override;

abstract class TestCase extends Orchestra
{
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            static fn (string $modelName): string => 'Hunter\Core\Database\Factories\\' . class_basename($modelName) . 'Factory',
        );
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');

        /*
         foreach (\Illuminate\Support\Facades\File::allFiles(__DIR__ . '/../database/migrations') as $migration) {
            (include $migration->getRealPath())->up();
         }
         */
    }

    protected function getPackageProviders($app)
    {
        return [
            CoreServiceProvider::class,
            DebuggerServiceProvider::class,
        ];
    }
}
