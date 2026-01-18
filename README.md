# Hunter Core

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hunter/core.svg?style=flat-square)](https://packagist.org/packages/hunter/core)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/hunter/core/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/hunter/core/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/hunter/core.svg?style=flat-square)](https://packagist.org/packages/hunter/core)

Hunter Core is a modular architecture foundation for Laravel applications. It provides the base infrastructure for building extensible, tenant-aware applications where functionality is delivered through installable modules.

## Features

- **Fluent Module Configuration** - Declarative API for defining modules using method chaining
- **Navigation System** - Unified navigation management across modules with NavItem and NavGroup
- **Module Registry** - Central registry for discovering and managing modules
- **Tenant-Aware Middleware** - Control module access per tenant
- **PHP 8.4 Property Hooks** - Modern PHP features for clean property access
- **Spatie Package Tools Integration** - Built on top of battle-tested spatie/laravel-package-tools

## Requirements

- PHP 8.4+
- Laravel 12+

## Installation

Install the package via Composer:

```bash
composer require hunter/core
```

The package auto-registers its service provider via Laravel's package discovery.

## Quick Start

Create a module by extending `ModuleServiceProvider`:

```php
<?php

declare(strict_types=1);

namespace Hunter\Analytics;

use Hunter\Core\Contracts\ModuleServiceProvider;
use Hunter\Core\Module\Module;
use Hunter\Core\Navigation\NavGroup;
use Hunter\Core\Navigation\NavItem;

final class AnalyticsServiceProvider extends ModuleServiceProvider
{
    public function configureModule(Module $module): void
    {
        $module
            ->identifier('hunter/analytics')
            ->name('hunter-analytics')
            ->description('Analytics and reporting for Hunter')
            ->version('1.0.0')
            ->hasConfig()
            ->hasViews()
            ->hasMigrations([
                'create_analytics_events_table',
                'create_analytics_reports_table',
            ])
            ->navigation([
                new NavGroup(
                    title: 'Analytics',
                    icon: 'chart-bar',
                    order: 50,
                    items: [
                        new NavItem('Dashboard', '/analytics', 'layout-dashboard', order: 1),
                        new NavItem('Reports', '/analytics/reports', 'file-text', order: 2),
                    ],
                ),
            ])
            ->author('Hunter Team', 'team@hunter.io')
            ->requiredPlatformVersion('1.0.0')
            ->dependencies(['hunter/core' => '^1.0']);
    }
}
```

## Module Configuration API

| Method                             | Description                                   |
| ---------------------------------- | --------------------------------------------- |
| `identifier(string)`               | Unique module identifier (vendor/name format) |
| `name(string)`                     | Package name for config, views, routes        |
| `description(string)`              | Human-readable description                    |
| `version(string)`                  | Semantic version                              |
| `hasConfig()`                      | Register config file                          |
| `hasViews()`                       | Register views directory                      |
| `hasRoutes()`                      | Register routes file                          |
| `hasTranslations()`                | Register translations                         |
| `hasMigration(string)`             | Register single migration                     |
| `hasMigrations(array)`             | Register multiple migrations                  |
| `hasCommand(string)`               | Register single command                       |
| `hasCommands(array)`               | Register multiple commands                    |
| `navigation(array)`                | Register navigation items                     |
| `author(string, ?string, ?string)` | Set author info                               |
| `requiredPlatformVersion(string)`  | Minimum platform version                      |
| `dependencies(array)`              | Module dependencies                           |

## Module Registry

Access registered modules via the registry:

```php
$registry = app('hunter.modules');

// Check if module exists
$registry->has('hunter/analytics');

// Get module provider
$provider = $registry->get('hunter/analytics');
$provider->identifier();  // 'hunter/analytics'
$provider->version();     // '1.0.0'

// Get all modules
$registry->all();
$registry->identifiers();
$registry->count();

// Get combined navigation
$registry->navigation();       // All items sorted by order
$registry->navigationItems();  // Only NavItem objects
$registry->navigationGroups(); // Only NavGroup objects
```

## Navigation

Create navigation items and groups:

```php
use Hunter\Core\Navigation\NavItem;
use Hunter\Core\Navigation\NavGroup;

// Simple item
$item = new NavItem(
    title: 'Dashboard',
    href: '/dashboard',
    icon: 'home',
    order: 10,
    badge: '3',        // Optional badge
    external: false,   // Opens in new tab
);

// Group with items
$group = new NavGroup(
    title: 'Settings',
    icon: 'settings',
    order: 100,
    collapsible: true,
    collapsed: false,
);

// Add items fluently (immutable)
$group = $group
    ->withItem(new NavItem('General', '/settings/general', order: 1))
    ->withItem(new NavItem('Security', '/settings/security', order: 2));

// Convert to array for frontend
$item->toArray();
$group->toArray();
```

## Middleware

Protect routes based on tenant module activation:

```php
use Hunter\Core\Http\Middleware\EnsureModuleActive;

// In routes
Route::get('/analytics', AnalyticsController::class)
    ->middleware(EnsureModuleActive::class . ':hunter/analytics');

// With alias (register in bootstrap/app.php)
Route::get('/analytics', AnalyticsController::class)
    ->middleware('module:hunter/analytics');
```

The middleware checks:

1. User is authenticated
2. User has a tenant (via `tenant()`, `tenant` property, or `currentTenant()`)
3. Tenant has the module active (via `hasModule()` or `modules()` relationship)

## Documentation

Full documentation is available in the [docs](docs/) directory:

1. [Index](docs/00-index.md) - Overview and architecture
2. [Installation](docs/01-installation.md) - Getting started
3. [Creating Modules](docs/02-creating-modules.md) - Complete module guide
4. [Navigation](docs/03-navigation.md) - NavItem and NavGroup
5. [Middleware](docs/04-middleware.md) - Route protection
6. [Registry](docs/05-registry.md) - Module registry API

## Testing

```bash
composer test
```

Run with coverage:

```bash
composer test-coverage
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Akira Foundation](https://github.com/akira-foundation)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
