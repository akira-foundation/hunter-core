<?php

declare(strict_types=1);

namespace Hunter\Module;

use Hunter\Module\Commands\ModuleCommand;
use Hunter\Module\Http\Middleware\EnsureModuleActive;
use Hunter\Module\Module\ModuleRegistry;
use Illuminate\Routing\Router;
use Override;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class HunterModuleServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('hunter-core')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_hunter_core_table')
            ->hasCommand(ModuleCommand::class);
    }

    #[Override]
    public function packageRegistered(): void
    {
        $this->app->singleton('hunter.modules', static fn (): ModuleRegistry => new ModuleRegistry());

        $this->app->alias('hunter.modules', ModuleRegistry::class);
    }

    #[Override]
    public function packageBooted(): void
    {
        $this->registerMiddlewareAlias();
    }

    private function registerMiddlewareAlias(): void
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);

        $router->aliasMiddleware('module.active', EnsureModuleActive::class);
    }
}
