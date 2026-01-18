# Installation

This guide covers installing Hunter Core in your Laravel application.

## Requirements

- PHP 8.4 or higher
- Laravel 12 or higher

## Installation via Composer

```bash
composer require hunter/core
```

The package will auto-register its service provider via Laravel's package discovery.

## Manual Service Provider Registration

If you have disabled auto-discovery, add the service provider to `bootstrap/providers.php`:

```php
<?php

return [
    // ...
    Hunter\Core\CoreServiceProvider::class,
];
```

## Verifying Installation

You can verify the installation by running the included Artisan command:

```bash
php artisan hunter-core
```

## Configuration

Hunter Core works out of the box without any configuration. However, you can publish the config file if needed:

```bash
php artisan vendor:publish --tag=hunter-core-config
```

## Module Registry

The module registry is automatically bound to the container as a singleton. You can access it via:

```php
// Via container binding
$registry = app('hunter.modules');

// Via class resolution
$registry = app(\Hunter\Core\Module\ModuleRegistry::class);

// Via dependency injection
public function __construct(
    private readonly ModuleRegistry $registry,
) {}
```

## Next Steps

Now that Hunter Core is installed, you can:

1. [Create your first module](02-creating-modules.md)
2. [Set up navigation](03-navigation.md)
3. [Protect routes with middleware](04-middleware.md)

---

[Previous: Index](00-index.md) | [Next: Creating Modules](02-creating-modules.md)
