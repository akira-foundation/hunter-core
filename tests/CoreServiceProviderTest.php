<?php

declare(strict_types=1);

use Core\Core\CoreServiceProvider;
use Spatie\LaravelPackageTools\Package;

it('registers the package correctly', function (): void {
    $provider = app()->getProvider(CoreServiceProvider::class);

    expect($provider)->toBeInstanceOf(CoreServiceProvider::class);
});

it('registers the command', function (): void {
    $commands = array_keys(Artisan::all());

    expect($commands)->toContain('hunter-core');
});

it('has the correct package name', function (): void {
    $provider = new CoreServiceProvider(app());

    $reflection = new ReflectionClass($provider);
    $method     = $reflection->getMethod('configurePackage');

    $package = new Package();
    $method->invoke($provider, $package);

    expect($package->name)->toBe('hunter-core');
});
