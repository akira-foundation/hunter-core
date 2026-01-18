# Navigation

Hunter Core provides a flexible navigation system that allows modules to contribute menu items to the platform's sidebar or header navigation.

## Navigation Components

There are two main navigation components:

- **NavItem** - A single navigation link
- **NavGroup** - A collapsible group containing multiple NavItems

## NavItem

A `NavItem` represents a single clickable navigation link.

### Constructor Parameters

```php
new NavItem(
    title: string,        // Display text
    href: string,         // URL or route path
    icon: ?string,        // Icon name (e.g., Lucide icon)
    order: int,           // Sort order (default: 0)
    badge: ?string,       // Optional badge text
    external: bool,       // Opens in new tab (default: false)
)
```

### Examples

```php
use Hunter\Core\Navigation\NavItem;

// Simple item
$dashboard = new NavItem('Dashboard', '/dashboard');

// Item with icon and order
$analytics = new NavItem(
    title: 'Analytics',
    href: '/analytics',
    icon: 'chart-bar',
    order: 10,
);

// Item with badge
$notifications = new NavItem(
    title: 'Notifications',
    href: '/notifications',
    icon: 'bell',
    badge: '3',
    order: 20,
);

// External link
$docs = new NavItem(
    title: 'Documentation',
    href: 'https://docs.hunter.io',
    icon: 'book-open',
    external: true,
    order: 100,
);
```

### Converting to Array

```php
$item = new NavItem('Dashboard', '/dashboard', 'home', order: 1);

$item->toArray();
// [
//     'title'    => 'Dashboard',
//     'href'     => '/dashboard',
//     'icon'     => 'home',
//     'order'    => 1,
//     'badge'    => null,
//     'external' => false,
// ]
```

## NavGroup

A `NavGroup` represents a collapsible section containing multiple `NavItem` objects.

### Constructor Parameters

```php
new NavGroup(
    title: string,              // Group display text
    items: array,               // Array of NavItem (default: [])
    icon: ?string,              // Icon name
    order: int,                 // Sort order (default: 0)
    collapsible: bool,          // Can be collapsed (default: true)
    collapsed: bool,            // Default collapsed state (default: false)
)
```

### Examples

```php
use Hunter\Core\Navigation\NavGroup;
use Hunter\Core\Navigation\NavItem;

// Group with items in constructor
$reports = new NavGroup(
    title: 'Reports',
    items: [
        new NavItem('Sales', '/reports/sales', order: 1),
        new NavItem('Users', '/reports/users', order: 2),
        new NavItem('Revenue', '/reports/revenue', order: 3),
    ],
    icon: 'file-text',
    order: 30,
);

// Group with fluent item addition
$settings = new NavGroup(
    title: 'Settings',
    icon: 'settings',
    order: 100,
    collapsible: true,
    collapsed: true,  // Start collapsed
);

$settings = $settings
    ->withItem(new NavItem('General', '/settings/general', order: 1))
    ->withItem(new NavItem('Security', '/settings/security', order: 2))
    ->withItem(new NavItem('Billing', '/settings/billing', order: 3));

// Add multiple items at once
$settings = $settings->withItems([
    new NavItem('API Keys', '/settings/api', order: 4),
    new NavItem('Webhooks', '/settings/webhooks', order: 5),
]);
```

### Immutability

`NavGroup` is immutable. Methods like `withItem()` and `withItems()` return a new instance:

```php
$group1 = new NavGroup('Settings', icon: 'settings');
$group2 = $group1->withItem(new NavItem('General', '/settings'));

// $group1 still has 0 items
// $group2 has 1 item
```

### Sorted Items

Items within a group are automatically sorted by their `order` property:

```php
$group = new NavGroup(
    title: 'Reports',
    items: [
        new NavItem('Third', '/third', order: 30),
        new NavItem('First', '/first', order: 10),
        new NavItem('Second', '/second', order: 20),
    ],
);

$sorted = $group->sortedItems();
// First (10), Second (20), Third (30)
```

### Converting to Array

```php
$group = new NavGroup(
    title: 'Settings',
    icon: 'settings',
    order: 100,
    items: [
        new NavItem('General', '/settings/general', order: 1),
    ],
);

$group->toArray();
// [
//     'title'       => 'Settings',
//     'icon'        => 'settings',
//     'order'       => 100,
//     'collapsible' => true,
//     'collapsed'   => false,
//     'items'       => [
//         [
//             'title'    => 'General',
//             'href'     => '/settings/general',
//             'icon'     => null,
//             'order'    => 1,
//             'badge'    => null,
//             'external' => false,
//         ],
//     ],
// ]
```

## Using Navigation in Modules

Register navigation in your module's `configureModule()` method:

```php
public function configureModule(Module $module): void
{
    $module
        ->identifier('hunter/crm')
        ->name('hunter-crm')
        ->navigation([
            // Top-level item
            new NavItem(
                title: 'CRM Dashboard',
                href: '/crm',
                icon: 'users',
                order: 10,
            ),

            // Group with nested items
            new NavGroup(
                title: 'Contacts',
                icon: 'contact',
                order: 20,
                items: [
                    new NavItem('All Contacts', '/crm/contacts', order: 1),
                    new NavItem('Companies', '/crm/companies', order: 2),
                    new NavItem('Import', '/crm/import', order: 3),
                ],
            ),

            // Another group
            new NavGroup(
                title: 'Sales',
                icon: 'dollar-sign',
                order: 30,
                items: [
                    new NavItem('Deals', '/crm/deals', order: 1),
                    new NavItem('Pipeline', '/crm/pipeline', order: 2),
                ],
            ),
        ]);
}
```

## Retrieving Navigation

Use the `ModuleRegistry` to get combined navigation from all modules:

```php
$registry = app('hunter.modules');

// All navigation (items and groups), sorted by order
$allNav = $registry->navigation();

// Only standalone NavItem objects
$items = $registry->navigationItems();

// Only NavGroup objects
$groups = $registry->navigationGroups();
```

## Order Values Best Practices

Use spaced order values to allow future insertions:

| Range | Purpose                        |
| ----- | ------------------------------ |
| 0-9   | System/Dashboard items         |
| 10-49 | Primary features               |
| 50-79 | Secondary features             |
| 80-99 | Tertiary features              |
| 100+  | Settings, help, external links |

Example:

```php
new NavItem('Dashboard', '/dashboard', order: 0),
new NavItem('Analytics', '/analytics', order: 10),
new NavItem('Reports', '/reports', order: 20),
new NavItem('Users', '/users', order: 30),
new NavItem('Settings', '/settings', order: 100),
```

## Frontend Integration

Navigation data can be passed to your frontend via Inertia:

```php
// In a middleware or service provider
Inertia::share('navigation', fn () => [
    'items' => collect(app('hunter.modules')->navigation())
        ->map(fn ($item) => $item->toArray())
        ->values()
        ->all(),
]);
```

Then in your React/Vue component:

```tsx
// React example
function Sidebar() {
    const { navigation } = usePage().props;

    return (
        <nav>
            {navigation.items.map((item) =>
                item.items ? (
                    <NavGroup key={item.title} {...item} />
                ) : (
                    <NavLink key={item.href} {...item} />
                ),
            )}
        </nav>
    );
}
```

---

[Previous: Creating Modules](02-creating-modules.md) | [Next: Middleware](04-middleware.md)
