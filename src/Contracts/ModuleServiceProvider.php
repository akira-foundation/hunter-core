<?php

declare(strict_types=1);

namespace Hunter\Core\Contracts;

use Hunter\Core\Module\Module;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

abstract class ModuleServiceProvider extends PackageServiceProvider implements ModuleManifest, ModuleNavigation
{
    /**
     * Configure the module using the fluent Module builder.
     */
    abstract public function configureModule(Module $module): void;

    /**
     * Configure the underlying Spatie package.
     * This is called automatically and delegates to configureModule().
     */
    final public function configurePackage(Package $package): void
    {
        $module = new Module($package);

        $this->configureModule($module);

        $module->apply();
    }

    /**
     * Boot the module service provider.
     */
    final public function bootingPackage(): void
    {
        $this->registerWithModuleRegistry();
    }

    /**
     * Default implementation - modules can override.
     */
    final public function navigation(): array
    {
        return [];
    }

    /**
     * Default implementation - modules can override.
     */
    final public function author(): ?array
    {
        return null;
    }

    /**
     * Default implementation - modules can override.
     */
    final public function requiredPlatformVersion(): ?string
    {
        return null;
    }

    /**
     * Default implementation - modules can override.
     */
    final public function dependencies(): array
    {
        return [];
    }

    /**
     * Register this module with the central registry.
     */
    protected function registerWithModuleRegistry(): void
    {
        if ($this->app->bound('hunter.modules')) {
            $this->app->make('hunter.modules')->register($this);
        }
    }
}
