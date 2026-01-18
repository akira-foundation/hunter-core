<?php

declare(strict_types=1);

namespace Hunter\Core\Contracts;

use Hunter\Core\Module\Module;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

abstract class ModuleServiceProvider extends PackageServiceProvider implements ModuleManifest, ModuleNavigation
{
    protected Module $module;

    abstract public function configureModule(Module $module): void;

    final public function configurePackage(Package $package): void
    {
        $this->module = new Module($package);

        $this->configureModule($this->module);

        $this->module->apply();
    }

    final public function bootingPackage(): void
    {
        $this->registerWithModuleRegistry();
    }

    final public function identifier(): string
    {
        return $this->module->identifier;
    }

    final public function name(): string
    {
        return $this->module->name;
    }

    final public function description(): string
    {
        return $this->module->description;
    }

    final public function version(): string
    {
        return $this->module->version;
    }

    final public function author(): ?array
    {
        return $this->module->author;
    }

    final public function requiredPlatformVersion(): ?string
    {
        return $this->module->requiredPlatformVersion;
    }

    /**
     * @return array<string, string>
     */
    final public function dependencies(): array
    {
        return $this->module->dependencies;
    }

    final public function navigation(): array
    {
        return $this->module->navigation;
    }

    final public function getModule(): Module
    {
        return $this->module;
    }

    private function registerWithModuleRegistry(): void
    {
        if ($this->app->bound('hunter.modules')) {
            $this->app->make('hunter.modules')->register($this);
        }
    }
}
