# Hunter Core

Hunter Core is a modular architecture foundation for Hunter Platform. It provides the base infrastructure for building
extensible, tenant-aware applications where functionality is delivered through installable modules.

## Features

- **Fluent Module Configuration** - Declarative API for defining modules using method chaining
- **Navigation System** - Unified navigation management across modules
- **Module Registry** - Central registry for discovering and managing modules
- **Tenant-Aware Middleware** - Control module access per tenant
- **Spatie Package Tools Integration** - Built on top of the battle-tested spatie/laravel-package-tools

## Requirements

- PHP 8.4+
- Laravel 12+
- spatie/laravel-package-tools ^2.0

## Documentation

1. [Installation](01-installation.md) - Getting started with Hunter Core
2. [Creating Modules](02-creating-modules.md) - How to create your own module
3. [Navigation](03-navigation.md) - Navigation items and groups
4. [Middleware](04-middleware.md) - Protecting routes with module middleware
5. [Registry](05-registry.md) - Working with the module registry

## Quick Example

```php
<?php

namespace Hunter\Analytics;

use Hunter\Module\Contracts\ModuleServiceProvider;
use Hunter\Module\Module\Module;
use Hunter\Module\Navigation\NavGroup;
use Hunter\Module\Navigation\NavItem;

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
                NavGroup::make('Analytics', 'chart-bar')
                    ->withItems([
                        new NavItem('Dashboard', '/analytics', 'layout-dashboard', order: 1),
                        new NavItem('Reports', '/analytics/reports', 'file-text', order: 2),
                    ]),
            ])
            ->author('Hunter Team', 'team@hunter.io')
            ->requiredPlatformVersion('1.0.0')
            ->dependencies(['hunter/core' => '^1.0']);
    }
}
```

## Architecture Overview

```
hunter-core/
├── src/
│   ├── Commands/           # Artisan commands
│   ├── Contracts/          # Interfaces and abstract classes
│   │   ├── ModuleManifest.php
│   │   ├── ModuleNavigation.php
│   │   └── ModuleServiceProvider.php
│   ├── Enums/              # Status and action enums
│   │   ├── ModuleLogAction.php
│   │   ├── ModuleLogStatus.php
│   │   └── ModuleStatus.php
│   ├── Http/
│   │   └── Middleware/
│   │       └── EnsureModuleActive.php
│   ├── Module/
│   │   ├── Module.php      # Fluent module builder
│   │   └── ModuleRegistry.php
│   └── Navigation/
│       ├── NavGroup.php
│       └── NavItem.php
└── docs/
```

---

[Next: Installation](01-installation.md)
