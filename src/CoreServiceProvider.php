<?php

declare(strict_types=1);

namespace Core\Core;

use Core\Core\Commands\CoreCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class CoreServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('hunter-core')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_hunter_core_table')
            ->hasCommand(CoreCommand::class);
    }
}
