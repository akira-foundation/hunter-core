<?php

declare(strict_types=1);

use Hunter\Core\Module\ModuleRegistry;

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
