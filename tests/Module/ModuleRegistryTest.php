<?php

declare(strict_types=1);

use Hunter\Module\Module\ModuleRegistry;
use Hunter\Module\Navigation\NavGroup;
use Hunter\Module\Navigation\NavItem;
use Hunter\Module\Tests\Fixtures\FakeAnalyticsModuleProvider;
use Hunter\Module\Tests\Fixtures\FakeCrmModuleProvider;

function createAnalyticsProvider(): FakeAnalyticsModuleProvider
{
    $provider = new FakeAnalyticsModuleProvider(app());
    $provider->register();

    return $provider;
}

function createCrmProvider(): FakeCrmModuleProvider
{
    $provider = new FakeCrmModuleProvider(app());
    $provider->register();

    return $provider;
}

describe('ModuleRegistry', function (): void {
    it('can be instantiated', function (): void {
        $registry = new ModuleRegistry();

        expect($registry)->toBeInstanceOf(ModuleRegistry::class)
            ->and($registry->count())->toBe(0);
    });

    it('returns empty arrays when no modules registered', function (): void {
        $registry = new ModuleRegistry();

        expect($registry->all())->toBe([])
            ->and($registry->identifiers())->toBe([])
            ->and($registry->navigation())->toBe([])
            ->and($registry->navigationItems())->toBe([])
            ->and($registry->navigationGroups())->toBe([]);
    });

    it('can check if module exists', function (): void {
        $registry = new ModuleRegistry();

        expect($registry->has('hunter/analytics'))->toBeFalse();
    });

    it('returns null for non-existent module', function (): void {
        $registry = new ModuleRegistry();

        expect($registry->get('hunter/analytics'))->toBeNull();
    });

    it('can be cleared', function (): void {
        $registry = new ModuleRegistry();
        $registry->clear();

        expect($registry->count())->toBe(0);
    });

    it('can register a module', function (): void {
        $registry = new ModuleRegistry();
        $provider = createAnalyticsProvider();

        $registry->register($provider);

        expect($registry->count())->toBe(1)
            ->and($registry->has('hunter/analytics'))->toBeTrue()
            ->and($registry->get('hunter/analytics'))->toBe($provider)
            ->and($registry->identifiers())->toBe(['hunter/analytics']);
    });

    it('can register multiple modules', function (): void {
        $registry  = new ModuleRegistry();
        $analytics = createAnalyticsProvider();
        $crm       = createCrmProvider();

        $registry->register($analytics);
        $registry->register($crm);

        expect($registry->count())->toBe(2)
            ->and($registry->has('hunter/analytics'))->toBeTrue()
            ->and($registry->has('hunter/crm'))->toBeTrue()
            ->and($registry->all())->toHaveCount(2);
    });

    it('returns all modules as array', function (): void {
        $registry  = new ModuleRegistry();
        $analytics = createAnalyticsProvider();
        $crm       = createCrmProvider();

        $registry->register($analytics);
        $registry->register($crm);

        $all = $registry->all();

        expect($all)->toHaveKey('hunter/analytics')
            ->and($all)->toHaveKey('hunter/crm')
            ->and($all['hunter/analytics'])->toBe($analytics)
            ->and($all['hunter/crm'])->toBe($crm);
    });

    it('collects navigation from all modules', function (): void {
        $registry = new ModuleRegistry();
        $registry->register(createAnalyticsProvider());
        $registry->register(createCrmProvider());

        $navigation = $registry->navigation();

        expect($navigation)->toHaveCount(4);
    });

    it('sorts navigation by order', function (): void {
        $registry = new ModuleRegistry();
        $registry->register(createAnalyticsProvider());
        $registry->register(createCrmProvider());

        $navigation = $registry->navigation();

        expect($navigation[0]->order)->toBe(5)
            ->and($navigation[1]->order)->toBe(10)
            ->and($navigation[2]->order)->toBe(15)
            ->and($navigation[3]->order)->toBe(20);
    });

    it('filters navigation items only', function (): void {
        $registry = new ModuleRegistry();
        $registry->register(createAnalyticsProvider());
        $registry->register(createCrmProvider());

        $items = $registry->navigationItems();

        expect($items)->toHaveCount(2)
            ->and($items[0])->toBeInstanceOf(NavItem::class)
            ->and($items[1])->toBeInstanceOf(NavItem::class);
    });

    it('filters navigation groups only', function (): void {
        $registry = new ModuleRegistry();
        $registry->register(createAnalyticsProvider());
        $registry->register(createCrmProvider());

        $groups = $registry->navigationGroups();

        expect($groups)->toHaveCount(2)
            ->and($groups[0])->toBeInstanceOf(NavGroup::class)
            ->and($groups[1])->toBeInstanceOf(NavGroup::class);
    });

    it('clears all registered modules', function (): void {
        $registry = new ModuleRegistry();
        $registry->register(createAnalyticsProvider());
        $registry->register(createCrmProvider());

        expect($registry->count())->toBe(2);

        $registry->clear();

        expect($registry->count())->toBe(0)
            ->and($registry->all())->toBe([]);
    });
});

describe('ModuleRegistry via container', function (): void {
    it('is registered as singleton', function (): void {
        $registry1 = app('hunter.modules');
        $registry2 = app('hunter.modules');

        expect($registry1)->toBe($registry2);
    });

    it('can be resolved by class name', function (): void {
        $registry = app(ModuleRegistry::class);

        expect($registry)->toBeInstanceOf(ModuleRegistry::class);
    });
});
