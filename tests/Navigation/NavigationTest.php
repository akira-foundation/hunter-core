<?php

declare(strict_types=1);

use Hunter\Core\Navigation\NavGroup;
use Hunter\Core\Navigation\NavItem;

describe('NavItem', function (): void {
    it('can be created with required parameters', function (): void {
        $item = new NavItem(
            title: 'Dashboard',
            href: '/dashboard',
        );

        expect($item->title)->toBe('Dashboard')
            ->and($item->href)->toBe('/dashboard')
            ->and($item->icon)->toBeNull()
            ->and($item->order)->toBe(0)
            ->and($item->badge)->toBeNull()
            ->and($item->external)->toBeFalse();
    });

    it('can be created with all parameters', function (): void {
        $item = new NavItem(
            title: 'Analytics',
            href: '/analytics',
            icon: 'chart-bar',
            order: 10,
            badge: 'New',
            external: true,
        );

        expect($item->title)->toBe('Analytics')
            ->and($item->href)->toBe('/analytics')
            ->and($item->icon)->toBe('chart-bar')
            ->and($item->order)->toBe(10)
            ->and($item->badge)->toBe('New')
            ->and($item->external)->toBeTrue();
    });

    it('can be converted to array', function (): void {
        $item = new NavItem(
            title: 'Settings',
            href: '/settings',
            icon: 'cog',
            order: 5,
        );

        $array = $item->toArray();

        expect($array)->toBe([
            'title'    => 'Settings',
            'href'     => '/settings',
            'icon'     => 'cog',
            'order'    => 5,
            'badge'    => null,
            'external' => false,
        ]);
    });
});

describe('NavGroup', function (): void {
    it('can be created with required parameters', function (): void {
        $group = new NavGroup(title: 'Admin');

        expect($group->title)->toBe('Admin')
            ->and($group->items)->toBe([])
            ->and($group->icon)->toBeNull()
            ->and($group->order)->toBe(0)
            ->and($group->collapsible)->toBeTrue()
            ->and($group->collapsed)->toBeFalse();
    });

    it('can add items with withItem', function (): void {
        $group = new NavGroup(title: 'Admin');
        $item  = new NavItem(title: 'Users', href: '/users');

        $newGroup = $group->withItem($item);

        expect($group->items)->toHaveCount(0)
            ->and($newGroup->items)->toHaveCount(1)
            ->and($newGroup->items[0])->toBe($item);
    });

    it('can add multiple items with withItems', function (): void {
        $group = new NavGroup(title: 'Admin');
        $items = [
            new NavItem(title: 'Users', href: '/users'),
            new NavItem(title: 'Roles', href: '/roles'),
        ];

        $newGroup = $group->withItems($items);

        expect($newGroup->items)->toHaveCount(2);
    });

    it('returns items sorted by order', function (): void {
        $group = new NavGroup(
            title: 'Admin',
            items: [
                new NavItem(title: 'Third', href: '/third', order: 30),
                new NavItem(title: 'First', href: '/first', order: 10),
                new NavItem(title: 'Second', href: '/second', order: 20),
            ],
        );

        $sorted = $group->sortedItems();

        expect($sorted[0]->title)->toBe('First')
            ->and($sorted[1]->title)->toBe('Second')
            ->and($sorted[2]->title)->toBe('Third');
    });

    it('can be converted to array', function (): void {
        $group = new NavGroup(
            title: 'Admin',
            items: [
                new NavItem(title: 'Users', href: '/users', order: 1),
            ],
            icon: 'shield',
            order: 5,
        );

        $array = $group->toArray();

        expect($array['title'])->toBe('Admin')
            ->and($array['icon'])->toBe('shield')
            ->and($array['order'])->toBe(5)
            ->and($array['collapsible'])->toBeTrue()
            ->and($array['collapsed'])->toBeFalse()
            ->and($array['items'])->toHaveCount(1)
            ->and($array['items'][0]['title'])->toBe('Users');
    });
});
