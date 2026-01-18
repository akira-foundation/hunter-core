# Creating Modules

This guide walks you through creating a Hunter module from scratch.

## Module Structure

A typical Hunter module follows this structure:

```
hunter-analytics/
├── src/
│   ├── AnalyticsServiceProvider.php
│   ├── Http/
│   │   └── Controllers/
│   ├── Models/
│   └── ...
├── config/
│   └── hunter-analytics.php
├── database/
│   └── migrations/
├── resources/
│   └── views/
├── routes/
│   └── web.php
├── composer.json
└── README.md
```

## Step 1: Create the Service Provider

Every module must have a service provider that extends `ModuleServiceProvider`:

```php
<?php

declare(strict_types=1);

namespace Hunter\Analytics;

use Hunter\Module\Contracts\ModuleServiceProvider;
use Hunter\Module\Module\Module;

final class AnalyticsServiceProvider extends ModuleServiceProvider
{
    public function configureModule(Module $module): void
    {
        $module
            ->identifier('hunter/analytics')
            ->name('hunter-analytics')
            ->description('Analytics and reporting for Hunter platform')
            ->version('1.0.0');
    }
}
```

## Step 2: Configure Module Metadata

The `Module` class provides a fluent API for configuration:

### Basic Information

```php
$module
    ->identifier('hunter/analytics')  // Unique identifier (vendor/name format)
    ->name('hunter-analytics')        // Package name (used for config, views, etc.)
    ->description('Analytics module') // Human-readable description
    ->version('1.0.0');               // Semantic version
```

### Author Information

```php
$module->author(
    name: 'Hunter Team',
    email: 'team@hunter.io',      // Optional
    url: 'https://hunter.io'      // Optional
);
```

### Platform Requirements

```php
$module
    ->requiredPlatformVersion('1.0.0')  // Minimum Hunter platform version
    ->dependencies([                     // Other module dependencies
        'hunter/core'  => '^1.0',
        'hunter/users' => '^2.0',
    ]);
```

## Step 3: Register Package Assets

Hunter Core integrates with Spatie Package Tools. Enable assets with fluent methods:

### Configuration

```php
// Registers config/hunter-analytics.php
$module->hasConfig();
```

### Views

```php
// Registers resources/views directory
$module->hasViews();
```

### Routes

```php
// Registers routes/hunter-analytics.php (uses package name)
$module->hasRoutes();
```

### Translations

```php
// Registers resources/lang directory
$module->hasTranslations();
```

### Migrations

```php
// Single migration
$module->hasMigration('create_analytics_events_table');

// Multiple migrations
$module->hasMigrations([
    'create_analytics_events_table',
    'create_analytics_reports_table',
    'create_analytics_dashboards_table',
]);
```

### Commands

```php
// Single command
$module->hasCommand(GenerateReportCommand::class);

// Multiple commands
$module->hasCommands([
    GenerateReportCommand::class,
    PruneEventsCommand::class,
    ExportDataCommand::class,
]);
```

## Step 4: Add Navigation

Modules can contribute navigation items to the platform:

```php
use Hunter\Module\Navigation\NavGroup;
use Hunter\Module\Navigation\NavItem;

$module->navigation([
    // Standalone item
    new NavItem(
        title: 'Dashboard',
        href: '/analytics',
        icon: 'chart-bar',
        order: 10,
    ),

    // Group with nested items
    new NavGroup(
        title: 'Reports',
        icon: 'file-text',
        order: 20,
        items: [
            new NavItem('Sales Report', '/analytics/reports/sales', order: 1),
            new NavItem('User Report', '/analytics/reports/users', order: 2),
        ],
    ),
]);
```

## Complete Example

Here's a complete module service provider:

```php
<?php

declare(strict_types=1);

namespace Hunter\Analytics;

use Hunter\Analytics\Commands\GenerateReportCommand;
use Hunter\Analytics\Commands\PruneEventsCommand;
use Hunter\Module\Contracts\ModuleServiceProvider;
use Hunter\Module\Module\Module;
use Hunter\Module\Navigation\NavGroup;
use Hunter\Module\Navigation\NavItem;

final class AnalyticsServiceProvider extends ModuleServiceProvider
{
    public function configureModule(Module $module): void
    {
        $module
            // Identification
            ->identifier('hunter/analytics')
            ->name('hunter-analytics')
            ->description('Analytics and reporting for Hunter platform')
            ->version('1.0.0')

            // Assets
            ->hasConfig()
            ->hasViews()
            ->hasRoutes()
            ->hasMigrations([
                'create_analytics_events_table',
                'create_analytics_reports_table',
            ])
            ->hasCommands([
                GenerateReportCommand::class,
                PruneEventsCommand::class,
            ])

            // Navigation
            ->navigation([
                new NavGroup(
                    title: 'Analytics',
                    icon: 'chart-bar',
                    order: 50,
                    items: [
                        new NavItem('Dashboard', '/analytics', 'layout-dashboard', order: 1),
                        new NavItem('Events', '/analytics/events', 'activity', order: 2),
                        new NavItem('Reports', '/analytics/reports', 'file-text', order: 3),
                    ],
                ),
            ])

            // Metadata
            ->author('Hunter Team', 'team@hunter.io', 'https://hunter.io')
            ->requiredPlatformVersion('1.0.0')
            ->dependencies([
                'hunter/core' => '^1.0',
            ]);
    }
}
```

## Step 5: Configure Composer

Your `composer.json` should autoload the service provider:

```json
{
    "name": "hunter/analytics",
    "description": "Analytics and reporting for Hunter platform",
    "type": "library",
    "require": {
        "php": "^8.4",
        "hunter/core": "^1.0"
    },
    "autoload": {
        "psr-4": {
            "Hunter\\Analytics\\": "src/"
        }
    },
    "extra": {
        "laravel": {
            "providers": ["Hunter\\Analytics\\AnalyticsServiceProvider"]
        }
    }
}
```

## Module Lifecycle

When a module is registered:

1. Laravel discovers the service provider via `composer.json`
2. `register()` is called, which triggers `configurePackage()`
3. `configureModule()` is called with a fresh `Module` instance
4. The module configuration is applied to the underlying Spatie Package
5. During `boot()`, the module registers itself with the `ModuleRegistry`

## Accessing Module Information

After registration, you can access module information:

```php
// Via the provider
$provider = app('hunter.modules')->get('hunter/analytics');
$provider->identifier();  // 'hunter/analytics'
$provider->name();        // 'hunter-analytics'
$provider->version();     // '1.0.0'
$provider->navigation();  // Array of NavItem|NavGroup

// Via the underlying Module object
$module = $provider->getModule();
$module->identifier;      // 'hunter/analytics' (PHP 8.4 property hooks)
$module->author;          // ['name' => 'Hunter Team', 'email' => '...', 'url' => '...']
```

## Best Practices

1. **Use semantic versioning** for your module version
2. **Declare dependencies** on other modules you rely on
3. **Keep navigation order values** spaced (10, 20, 30) to allow insertion
4. **Use descriptive identifiers** in vendor/name format
5. **Provide meaningful descriptions** for the module registry

---

[Previous: Installation](01-installation.md) | [Next: Navigation](03-navigation.md)
