<?php

declare(strict_types=1);

use Hunter\Core\Module\Module;
use Spatie\LaravelPackageTools\Package;

describe('Module fluent builder', function (): void {
    it('can set name', function (): void {
        $package = new Package();
        $module  = new Module($package);

        $result = $module->name('hunter-analytics');

        expect($result)->toBe($module)
            ->and($module->getName())->toBe('hunter-analytics');
    });

    it('can enable config', function (): void {
        $package = new Package();
        $module  = new Module($package);

        $result = $module->hasConfig();

        expect($result)->toBe($module);
    });

    it('can enable views', function (): void {
        $package = new Package();
        $module  = new Module($package);

        $result = $module->hasViews();

        expect($result)->toBe($module);
    });

    it('can enable routes', function (): void {
        $package = new Package();
        $module  = new Module($package);

        $result = $module->hasRoutes();

        expect($result)->toBe($module);
    });

    it('can enable translations', function (): void {
        $package = new Package();
        $module  = new Module($package);

        $result = $module->hasTranslations();

        expect($result)->toBe($module);
    });

    it('can add single migration', function (): void {
        $package = new Package();
        $module  = new Module($package);

        $result = $module->hasMigration('create_analytics_table');

        expect($result)->toBe($module);
    });

    it('can add multiple migrations', function (): void {
        $package = new Package();
        $module  = new Module($package);

        $result = $module->hasMigrations([
            'create_analytics_table',
            'create_metrics_table',
        ]);

        expect($result)->toBe($module);
    });

    it('can add single command', function (): void {
        $package = new Package();
        $module  = new Module($package);

        $result = $module->hasCommand('App\Commands\AnalyticsCommand');

        expect($result)->toBe($module);
    });

    it('can add multiple commands', function (): void {
        $package = new Package();
        $module  = new Module($package);

        $result = $module->hasCommands([
            'App\Commands\AnalyticsCommand',
            'App\Commands\ReportCommand',
        ]);

        expect($result)->toBe($module);
    });

    it('supports fluent chaining', function (): void {
        $package = new Package();
        $module  = new Module($package);

        $module
            ->name('hunter-analytics')
            ->hasConfig()
            ->hasViews()
            ->hasRoutes()
            ->hasTranslations()
            ->hasMigration('create_analytics_table')
            ->hasCommand('App\Commands\AnalyticsCommand');

        expect($module->getName())->toBe('hunter-analytics');
    });

    it('applies configuration to package', function (): void {
        $package = new Package();
        $module  = new Module($package);

        $module
            ->name('hunter-analytics')
            ->hasConfig()
            ->hasViews()
            ->hasMigration('create_analytics_table')
            ->hasCommand('App\Commands\TestCommand')
            ->apply();

        expect($package->name)->toBe('hunter-analytics');
    });

    it('applies routes to package', function (): void {
        $package = new Package();
        $module  = new Module($package);

        $module
            ->name('hunter-analytics')
            ->hasRoutes()
            ->apply();

        expect($package->name)->toBe('hunter-analytics');
    });

    it('applies translations to package', function (): void {
        $package = new Package();
        $module  = new Module($package);

        $module
            ->name('hunter-analytics')
            ->hasTranslations()
            ->apply();

        expect($package->name)->toBe('hunter-analytics');
    });

    it('applies multiple migrations to package', function (): void {
        $package = new Package();
        $module  = new Module($package);

        $module
            ->name('hunter-analytics')
            ->hasMigrations(['create_first_table', 'create_second_table'])
            ->apply();

        expect($package->name)->toBe('hunter-analytics');
    });

    it('applies multiple commands to package', function (): void {
        $package = new Package();
        $module  = new Module($package);

        $module
            ->name('hunter-analytics')
            ->hasCommands(['App\Commands\FirstCommand', 'App\Commands\SecondCommand'])
            ->apply();

        expect($package->name)->toBe('hunter-analytics');
    });

    it('returns empty name by default', function (): void {
        $package = new Package();
        $module  = new Module($package);

        expect($module->getName())->toBe('');
    });
});
