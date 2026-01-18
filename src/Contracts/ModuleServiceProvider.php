<?php

declare(strict_types=1);

namespace Hunter\Core\Contracts;

use Hunter\Core\Module\Module;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

abstract class ModuleServiceProvider extends PackageServiceProvider implements ModuleManifest, ModuleNavigation
{
    abstract public function configureModule(Module $module): void;

    final public function configurePackage(Package $package): void
    {
        $module = new Module($package);

        $this->configureModule($module);

        $module->apply();
    }

    final public function bootingPackage(): void
    {
        $this->registerWithModuleRegistry();
    }

    final public function navigation(): array
    {
        return [];
    }

    final public function author(): ?array
    {
        return null;
    }

    final public function requiredPlatformVersion(): ?string
    {
        return null;
    }

    final public function dependencies(): array
    {
        return [];
    }

    protected function registerWithModuleRegistry(): void
    {
        if ($this->app->bound('hunter.modules')) {
            $this->app->make('hunter.modules')->register($this);
        }
    }
}
