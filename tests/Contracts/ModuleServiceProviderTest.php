<?php

declare(strict_types=1);

namespace Hunter\Core\Tests\Contracts;

use Hunter\Core\Contracts\ModuleServiceProvider;
use Hunter\Core\Module\Module;
use Hunter\Core\Module\ModuleRegistry;
use Hunter\Core\Navigation\NavItem;

final class ModuleServiceProviderTest extends ModuleServiceProvider
{
    public function configureModule(Module $module): void
    {
        $module
            ->identifier('test/module')
            ->name('test-module')
            ->description('Test module for testing')
            ->version('1.2.3')
            ->navigation([
                new NavItem('Test', '/test', order: 1),
            ])
            ->author('Test Author', 'test@example.com', 'https://example.com')
            ->requiredPlatformVersion('2.0.0')
            ->dependencies(['hunter/core' => '^1.0']);
    }
}

final class MinimalModuleProvider extends ModuleServiceProvider
{
    public function configureModule(Module $module): void
    {
        $module->name('minimal');
    }
}

describe('ModuleServiceProvider', function (): void {
    it('configures module via fluent builder', function (): void {
        $provider = new ModuleServiceProviderTest(app());
        $provider->register();

        expect($provider->identifier())->toBe('test/module')
            ->and($provider->name())->toBe('test-module')
            ->and($provider->description())->toBe('Test module for testing')
            ->and($provider->version())->toBe('1.2.3');
    });

    it('provides navigation from module', function (): void {
        $provider = new ModuleServiceProviderTest(app());
        $provider->register();

        $navigation = $provider->navigation();

        expect($navigation)->toHaveCount(1)
            ->and($navigation[0])->toBeInstanceOf(NavItem::class)
            ->and($navigation[0]->title)->toBe('Test');
    });

    it('provides author from module', function (): void {
        $provider = new ModuleServiceProviderTest(app());
        $provider->register();

        $author = $provider->author();

        expect($author)->toBe([
            'name'  => 'Test Author',
            'email' => 'test@example.com',
            'url'   => 'https://example.com',
        ]);
    });

    it('provides required platform version from module', function (): void {
        $provider = new ModuleServiceProviderTest(app());
        $provider->register();

        expect($provider->requiredPlatformVersion())->toBe('2.0.0');
    });

    it('provides dependencies from module', function (): void {
        $provider = new ModuleServiceProviderTest(app());
        $provider->register();

        expect($provider->dependencies())->toBe(['hunter/core' => '^1.0']);
    });

    it('provides access to underlying module', function (): void {
        $provider = new ModuleServiceProviderTest(app());
        $provider->register();

        $module = $provider->getModule();

        expect($module)->toBeInstanceOf(Module::class)
            ->and($module->identifier)->toBe('test/module');
    });

    it('returns defaults when module not configured', function (): void {
        $provider = new MinimalModuleProvider(app());
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

        $provider = new ModuleServiceProviderTest(app());
        $provider->register();
        $provider->boot();

        expect($registry->has('test/module'))->toBeTrue()
            ->and($registry->get('test/module'))->toBe($provider);
    });

    it('does not fail when registry not bound', function (): void {
        $app = app();
        $app->offsetUnset('hunter.modules');

        $provider = new ModuleServiceProviderTest($app);
        $provider->register();
        $provider->boot();

        expect(true)->toBeTrue();

        $app->singleton('hunter.modules', ModuleRegistry::class);
    });
});
