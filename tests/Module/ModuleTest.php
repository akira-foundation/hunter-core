<?php

declare(strict_types=1);

use Hunter\Core\Module\Module;
use Hunter\Core\Navigation\NavItem;
use Spatie\LaravelPackageTools\Package;

describe('Module fluent builder', function (): void {
    it('can set identifier', function (): void {
        $package = new Package();
        $module  = new Module($package);

        $result = $module->identifier('hunter/analytics');

        expect($result)->toBe($module)
            ->and($module->identifier)->toBe('hunter/analytics');
    });

    it('returns empty identifier by default', function (): void {
        $package = new Package();
        $module  = new Module($package);

        expect($module->identifier)->toBe('');
    });

    it('can set name', function (): void {
        $package = new Package();
        $module  = new Module($package);

        $result = $module->name('hunter-analytics');

        expect($result)->toBe($module)
            ->and($module->name)->toBe('hunter-analytics');
    });

    it('can set description', function (): void {
        $package = new Package();
        $module  = new Module($package);

        $result = $module->description('Analytics module for tracking metrics');

        expect($result)->toBe($module)
            ->and($module->description)->toBe('Analytics module for tracking metrics');
    });

    it('returns empty description by default', function (): void {
        $package = new Package();
        $module  = new Module($package);

        expect($module->description)->toBe('');
    });

    it('can set version', function (): void {
        $package = new Package();
        $module  = new Module($package);

        $result = $module->version('2.0.0');

        expect($result)->toBe($module)
            ->and($module->version)->toBe('2.0.0');
    });

    it('returns default version', function (): void {
        $package = new Package();
        $module  = new Module($package);

        expect($module->version)->toBe('1.0.0');
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

    it('can set navigation', function (): void {
        $package = new Package();
        $module  = new Module($package);
        $navItem = new NavItem('Dashboard', '/dashboard');

        $result = $module->navigation([$navItem]);

        expect($result)->toBe($module)
            ->and($module->navigation)->toBe([$navItem]);
    });

    it('returns empty navigation by default', function (): void {
        $package = new Package();
        $module  = new Module($package);

        expect($module->navigation)->toBe([]);
    });

    it('can set author with name only', function (): void {
        $package = new Package();
        $module  = new Module($package);

        $result = $module->author('Hunter Team');

        expect($result)->toBe($module)
            ->and($module->author)->toBe([
                'name'  => 'Hunter Team',
                'email' => null,
                'url'   => null,
            ]);
    });

    it('can set author with all details', function (): void {
        $package = new Package();
        $module  = new Module($package);

        $result = $module->author('Hunter Team', 'team@hunter.io', 'https://hunter.io');

        expect($result)->toBe($module)
            ->and($module->author)->toBe([
                'name'  => 'Hunter Team',
                'email' => 'team@hunter.io',
                'url'   => 'https://hunter.io',
            ]);
    });

    it('returns null author by default', function (): void {
        $package = new Package();
        $module  = new Module($package);

        expect($module->author)->toBeNull();
    });

    it('can set required platform version', function (): void {
        $package = new Package();
        $module  = new Module($package);

        $result = $module->requiredPlatformVersion('1.0.0');

        expect($result)->toBe($module)
            ->and($module->requiredPlatformVersion)->toBe('1.0.0');
    });

    it('returns null required platform version by default', function (): void {
        $package = new Package();
        $module  = new Module($package);

        expect($module->requiredPlatformVersion)->toBeNull();
    });

    it('can set dependencies', function (): void {
        $package = new Package();
        $module  = new Module($package);

        $result = $module->dependencies([
            'hunter/core'  => '^1.0',
            'hunter/users' => '^1.0',
        ]);

        expect($result)->toBe($module)
            ->and($module->dependencies)->toBe([
                'hunter/core'  => '^1.0',
                'hunter/users' => '^1.0',
            ]);
    });

    it('returns empty dependencies by default', function (): void {
        $package = new Package();
        $module  = new Module($package);

        expect($module->dependencies)->toBe([]);
    });

    it('supports fluent chaining', function (): void {
        $package = new Package();
        $module  = new Module($package);
        $navItem = new NavItem('Dashboard', '/dashboard');

        $module
            ->identifier('hunter/analytics')
            ->name('hunter-analytics')
            ->description('Analytics module')
            ->version('2.0.0')
            ->hasConfig()
            ->hasViews()
            ->hasRoutes()
            ->hasTranslations()
            ->hasMigration('create_analytics_table')
            ->hasCommand('App\Commands\AnalyticsCommand')
            ->navigation([$navItem])
            ->author('Hunter Team', 'team@hunter.io')
            ->requiredPlatformVersion('1.0.0')
            ->dependencies(['hunter/core' => '^1.0']);

        expect($module->identifier)->toBe('hunter/analytics')
            ->and($module->name)->toBe('hunter-analytics')
            ->and($module->description)->toBe('Analytics module')
            ->and($module->version)->toBe('2.0.0')
            ->and($module->navigation)->toBe([$navItem])
            ->and($module->author)->toBe([
                'name'  => 'Hunter Team',
                'email' => 'team@hunter.io',
                'url'   => null,
            ])
            ->and($module->requiredPlatformVersion)->toBe('1.0.0')
            ->and($module->dependencies)->toBe(['hunter/core' => '^1.0']);
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

        expect($module->name)->toBe('');
    });
});
