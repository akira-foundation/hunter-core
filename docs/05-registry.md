# Module Registry

The `ModuleRegistry` is a central service that tracks all registered modules in the application. It provides methods for querying modules and aggregating their navigation.

## Accessing the Registry

### Via Container Binding

```php
$registry = app('hunter.modules');
```

### Via Class Resolution

```php
use Hunter\Core\Module\ModuleRegistry;

$registry = app(ModuleRegistry::class);
```

### Via Dependency Injection

```php
use Hunter\Core\Module\ModuleRegistry;

final class NavigationController
{
    public function __construct(
        private readonly ModuleRegistry $registry,
    ) {}

    public function index(): array
    {
        return $this->registry->navigation();
    }
}
```

## API Reference

### `register(ModuleServiceProvider $provider): void`

Registers a module with the registry. This is called automatically during module boot.

```php
$registry->register($provider);
```

### `has(string $identifier): bool`

Checks if a module is registered.

```php
if ($registry->has('hunter/analytics')) {
    // Module is registered
}
```

### `get(string $identifier): ?ModuleServiceProvider`

Gets a registered module by its identifier. Returns `null` if not found.

```php
$analytics = $registry->get('hunter/analytics');

if ($analytics !== null) {
    echo $analytics->name();        // 'hunter-analytics'
    echo $analytics->version();     // '1.0.0'
    echo $analytics->description(); // 'Analytics and reporting'
}
```

### `all(): array<string, ModuleServiceProvider>`

Returns all registered modules as an associative array keyed by identifier.

```php
$modules = $registry->all();

foreach ($modules as $identifier => $provider) {
    echo "{$identifier}: {$provider->name()} v{$provider->version()}";
}
```

### `identifiers(): array<int, string>`

Returns an array of all registered module identifiers.

```php
$identifiers = $registry->identifiers();
// ['hunter/core', 'hunter/analytics', 'hunter/crm']
```

### `count(): int`

Returns the number of registered modules.

```php
$count = $registry->count();
// 3
```

### `clear(): void`

Removes all registered modules. Primarily useful for testing.

```php
$registry->clear();
```

## Navigation Methods

### `navigation(): array<int, NavItem|NavGroup>`

Returns combined navigation from all modules, sorted by order.

```php
$navigation = $registry->navigation();

foreach ($navigation as $item) {
    if ($item instanceof NavGroup) {
        echo "Group: {$item->title}";
    } else {
        echo "Item: {$item->title} -> {$item->href}";
    }
}
```

### `navigationItems(): array<int, NavItem>`

Returns only standalone navigation items (excludes groups).

```php
$items = $registry->navigationItems();

foreach ($items as $item) {
    echo "<a href=\"{$item->href}\">{$item->title}</a>";
}
```

### `navigationGroups(): array<int, NavGroup>`

Returns only navigation groups (excludes standalone items).

```php
$groups = $registry->navigationGroups();

foreach ($groups as $group) {
    echo "<div class=\"nav-group\">";
    echo "<h3>{$group->title}</h3>";

    foreach ($group->sortedItems() as $item) {
        echo "<a href=\"{$item->href}\">{$item->title}</a>";
    }

    echo "</div>";
}
```

## Practical Examples

### Building a Sidebar Component

```php
// In a controller or middleware
public function share(): array
{
    $registry = app('hunter.modules');

    return [
        'navigation' => collect($registry->navigation())
            ->map(fn ($item) => $item->toArray())
            ->values()
            ->all(),
    ];
}
```

### Listing Installed Modules

```php
// In an admin controller
public function modules(): Response
{
    $registry = app('hunter.modules');

    $modules = collect($registry->all())->map(fn ($provider) => [
        'identifier'  => $provider->identifier(),
        'name'        => $provider->name(),
        'description' => $provider->description(),
        'version'     => $provider->version(),
        'author'      => $provider->author(),
    ]);

    return Inertia::render('Admin/Modules', [
        'modules' => $modules,
    ]);
}
```

### Checking Module Dependencies

```php
public function checkDependencies(string $identifier): array
{
    $registry = app('hunter.modules');
    $provider = $registry->get($identifier);

    if ($provider === null) {
        return ['error' => 'Module not found'];
    }

    $missing = [];

    foreach ($provider->dependencies() as $dep => $version) {
        if (! $registry->has($dep)) {
            $missing[] = "{$dep} ({$version})";
        }
    }

    return [
        'module'  => $identifier,
        'missing' => $missing,
        'valid'   => empty($missing),
    ];
}
```

### Module-Aware Feature Flags

```php
final class FeatureService
{
    public function __construct(
        private readonly ModuleRegistry $registry,
    ) {}

    public function isAvailable(string $feature): bool
    {
        return match ($feature) {
            'analytics'  => $this->registry->has('hunter/analytics'),
            'crm'        => $this->registry->has('hunter/crm'),
            'invoicing'  => $this->registry->has('hunter/invoicing'),
            default      => false,
        };
    }
}
```

### Aggregating Module Commands

```php
// Get all commands from all modules
$commands = collect($registry->all())
    ->flatMap(fn ($provider) => $provider->getModule()?->commands ?? [])
    ->unique()
    ->values()
    ->all();
```

## Service Provider Registration

The registry is registered as a singleton in `CoreServiceProvider`:

```php
// Bound as singleton
$this->app->singleton('hunter.modules', ModuleRegistry::class);

// Also aliased to the class
$this->app->alias('hunter.modules', ModuleRegistry::class);
```

## Module Auto-Registration

Modules automatically register themselves during boot:

```php
// In ModuleServiceProvider
final public function bootingPackage(): void
{
    $this->registerWithModuleRegistry();
}

private function registerWithModuleRegistry(): void
{
    if ($this->app->bound('hunter.modules')) {
        $this->app->make('hunter.modules')->register($this);
    }
}
```

This means:

1. Core package boots first and registers the `ModuleRegistry`
2. Other modules boot and register themselves with the registry
3. All modules are available in the registry after boot completes

## Testing with the Registry

```php
use Hunter\Core\Module\ModuleRegistry;

beforeEach(function () {
    app(ModuleRegistry::class)->clear();
});

it('registers modules correctly', function () {
    $registry = app(ModuleRegistry::class);

    expect($registry->count())->toBe(0);

    // Manually trigger provider registration
    $provider = new AnalyticsServiceProvider(app());
    $provider->register();
    $provider->boot();

    expect($registry->count())->toBe(1)
        ->and($registry->has('hunter/analytics'))->toBeTrue();
});

it('aggregates navigation from multiple modules', function () {
    $registry = app(ModuleRegistry::class);

    // Register multiple modules
    (new AnalyticsServiceProvider(app()))->register()->boot();
    (new CrmServiceProvider(app()))->register()->boot();

    $navigation = $registry->navigation();

    expect($navigation)->toHaveCount(4);  // 2 items per module
});
```

---

[Previous: Middleware](04-middleware.md) | [Index](00-index.md)
