<?php

declare(strict_types=1);

use Hunter\Module\HunterModuleServiceProvider;
use Spatie\LaravelPackageTools\Package;

it('registers the package correctly', function (): void {
    $provider = app()->getProvider(HunterModuleServiceProvider::class);

    expect($provider)->toBeInstanceOf(HunterModuleServiceProvider::class);
});

it('registers the command', function (): void {
    $commands = array_keys(Artisan::all());

    expect($commands)->toContain('hunter:module');
});

it('has the correct package name', function (): void {
    $provider = new HunterModuleServiceProvider(app());

    $reflection = new ReflectionClass($provider);
    $method     = $reflection->getMethod('configurePackage');

    $package = new Package();
    $method->invoke($provider, $package);

    expect($package->name)->toBe('hunter-core');
});
