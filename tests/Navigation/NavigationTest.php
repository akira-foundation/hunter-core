<?php

declare(strict_types=1);

use Hunter\Core\Navigation\NavGroup;
use Hunter\Core\Navigation\NavItem;

describe('NavItem', function (): void {
    it('can be created with make()', function (): void {
        $item = NavItem::make('Dashboard')
            ->href('/dashboard');

        expect($item->title)->toBe('Dashboard')
            ->and($item->href)->toBe('/dashboard')
            ->and($item->icon)->toBeNull()
            ->and($item->order)->toBe(0)
            ->and($item->badge)->toBeNull()
            ->and($item->external)->toBeFalse();
    });

    it('can be created with all fluent methods', function (): void {
        $item = NavItem::make()
            ->title('Analytics')
            ->href('/analytics')
            ->icon('chart-bar')
            ->order(10)
            ->badge('New')
            ->external();

        expect($item->title)->toBe('Analytics')
            ->and($item->href)->toBe('/analytics')
            ->and($item->icon)->toBe('chart-bar')
            ->and($item->order)->toBe(10)
            ->and($item->badge)->toBe('New')
            ->and($item->external)->toBeTrue();
    });

    it('has url() as alias for href()', function (): void {
        $item = NavItem::make('Test')->url('/test-url');

        expect($item->href)->toBe('/test-url');
    });

    it('has route() as alias for href()', function (): void {
        $item = NavItem::make('Test')->route('dashboard.index');

        expect($item->href)->toBe('dashboard.index');
    });

    it('has openInNewTab() as alias for external()', function (): void {
        $item = NavItem::make('Test')->href('/test')->openInNewTab();

        expect($item->external)->toBeTrue();
    });

    it('can be converted to array', function (): void {
        $item = NavItem::make('Settings')
            ->href('/settings')
            ->icon('cog')
            ->order(5);

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
    it('can be created with make()', function (): void {
        $group = NavGroup::make('Admin');

        expect($group->title)->toBe('Admin')
            ->and($group->items)->toBe([])
            ->and($group->icon)->toBeNull()
            ->and($group->order)->toBe(0)
            ->and($group->collapsible)->toBeTrue()
            ->and($group->collapsed)->toBeFalse();
    });

    it('can be created with all fluent methods', function (): void {
        $group = NavGroup::make()
            ->title('Settings')
            ->icon('cog')
            ->order(50)
            ->collapsible()
            ->collapsed();

        expect($group->title)->toBe('Settings')
            ->and($group->icon)->toBe('cog')
            ->and($group->order)->toBe(50)
            ->and($group->collapsible)->toBeTrue()
            ->and($group->collapsed)->toBeTrue();
    });

    it('can add single item with item()', function (): void {
        $item = NavItem::make('Users')->href('/users');

        $group = NavGroup::make('Admin')->item($item);

        expect($group->items)->toHaveCount(1)
            ->and($group->items[0])->toBe($item);
    });

    it('can add multiple items with items()', function (): void {
        $items = [
            NavItem::make('Users')->href('/users'),
            NavItem::make('Roles')->href('/roles'),
        ];

        $group = NavGroup::make('Admin')->items($items);

        expect($group->items)->toHaveCount(2);
    });

    it('can chain item() calls', function (): void {
        $group = NavGroup::make('Admin')
            ->item(NavItem::make('Users')->href('/users'))
            ->item(NavItem::make('Roles')->href('/roles'));

        expect($group->items)->toHaveCount(2);
    });

    it('returns items sorted by order', function (): void {
        $group = NavGroup::make('Admin')
            ->items([
                NavItem::make('Third')->href('/third')->order(30),
                NavItem::make('First')->href('/first')->order(10),
                NavItem::make('Second')->href('/second')->order(20),
            ]);

        $sorted = $group->sortedItems();

        expect($sorted[0]->title)->toBe('First')
            ->and($sorted[1]->title)->toBe('Second')
            ->and($sorted[2]->title)->toBe('Third');
    });

    it('can be converted to array', function (): void {
        $group = NavGroup::make('Admin')
            ->icon('shield')
            ->order(5)
            ->items([
                NavItem::make('Users')->href('/users')->order(1),
            ]);

        $array = $group->toArray();

        expect($array['title'])->toBe('Admin')
            ->and($array['icon'])->toBe('shield')
            ->and($array['order'])->toBe(5)
            ->and($array['collapsible'])->toBeTrue()
            ->and($array['collapsed'])->toBeFalse()
            ->and($array['items'])->toHaveCount(1)
            ->and($array['items'][0]['title'])->toBe('Users');
    });

    it('can disable collapsible', function (): void {
        $group = NavGroup::make('Admin')->collapsible(false);

        expect($group->collapsible)->toBeFalse();
    });
});
