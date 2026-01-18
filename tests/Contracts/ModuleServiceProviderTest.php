<?php

declare(strict_types=1);

use Hunter\Module\Module\Module;
use Hunter\Module\Module\ModuleRegistry;
use Hunter\Module\Navigation\NavItem;
use Hunter\Module\Tests\Fixtures\MinimalModuleServiceProvider;
use Hunter\Module\Tests\Fixtures\TestModuleServiceProvider;

describe('ModuleServiceProvider', function (): void {
    it('configures module via fluent builder', function (): void {
        $provider = new TestModuleServiceProvider(app());
        $provider->register();

        expect($provider->identifier())->toBe('test/module')
            ->and($provider->name())->toBe('test-module')
            ->and($provider->description())->toBe('Test module for testing')
            ->and($provider->version())->toBe('1.2.3');
    });

    it('provides navigation from module', function (): void {
        $provider = new TestModuleServiceProvider(app());
        $provider->register();

        $navigation = $provider->navigation();

        expect($navigation)->toHaveCount(1)
            ->and($navigation[0])->toBeInstanceOf(NavItem::class)
            ->and($navigation[0]->title)->toBe('Test');
    });

    it('provides author from module', function (): void {
        $provider = new TestModuleServiceProvider(app());
        $provider->register();

        $author = $provider->author();

        expect($author)->toBe([
            'name'  => 'Test Author',
            'email' => 'test@example.com',
            'url'   => 'https://example.com',
        ]);
    });

    it('provides required platform version from module', function (): void {
        $provider = new TestModuleServiceProvider(app());
        $provider->register();

        expect($provider->requiredPlatformVersion())->toBe('2.0.0');
    });

    it('provides dependencies from module', function (): void {
        $provider = new TestModuleServiceProvider(app());
        $provider->register();

        expect($provider->dependencies())->toBe(['hunter/core' => '^1.0']);
    });

    it('provides access to underlying module', function (): void {
        $provider = new TestModuleServiceProvider(app());
        $provider->register();

        $module = $provider->getModule();

        expect($module)->toBeInstanceOf(Module::class)
            ->and($module->identifier)->toBe('test/module');
    });

    it('returns defaults when module not configured', function (): void {
        $provider = new MinimalModuleServiceProvider(app());
        $provider->register();

        expect($provider->identifier())->toBe('')
            ->and($provider->name())->toBe('minimal')
            ->and($provider->description())->toBe('')
            ->and($provider->version())->toBe('1.0.0')
            ->and($provider->navigation())->toBe([])
            ->and($provider->author())->toBeNull()
            ->and($provider->requiredPlatformVersion())->toBeNull()
            ->and($provider->dependencies())->toBe([]);
    });

    it('registers with module registry on boot', function (): void {
        $registry = app(ModuleRegistry::class);
        $registry->clear();

        $provider = new TestModuleServiceProvider(app());
        $provider->register();
        $provider->boot();

        expect($registry->has('test/module'))->toBeTrue()
            ->and($registry->get('test/module'))->toBe($provider);
    });

    it('does not fail when registry not bound', function (): void {
        $app = app();
        $app->offsetUnset('hunter.modules');

        $provider = new TestModuleServiceProvider($app);
        $provider->register();
        $provider->boot();

        expect(true)->toBeTrue();

        $app->singleton('hunter.modules', ModuleRegistry::class);
    });
});
