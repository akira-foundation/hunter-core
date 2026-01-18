# Middleware

Hunter Core provides the `EnsureModuleActive` middleware for protecting routes based on tenant module activation.

## Overview

The `EnsureModuleActive` middleware checks whether a specific module is active for the current user's tenant before allowing access to a route. This is essential for multi-tenant applications where different tenants may have different modules enabled.

## Basic Usage

### Route Definition

```php
use Hunter\Module\Http\Middleware\EnsureModuleActive;

// Single route
Route::get('/analytics', AnalyticsController::class)
    ->middleware(EnsureModuleActive::class . ':hunter/analytics');

// Route group
Route::middleware([EnsureModuleActive::class . ':hunter/crm'])
    ->prefix('crm')
    ->group(function () {
        Route::get('/', [CrmController::class, 'index']);
        Route::get('/contacts', [CrmController::class, 'contacts']);
        Route::get('/deals', [CrmController::class, 'deals']);
    });
```

### Using Route Attributes

```php
use Hunter\Module\Http\Middleware\EnsureModuleActive;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Middleware;

#[Middleware(EnsureModuleActive::class . ':hunter/analytics')]
final class AnalyticsController
{
    #[Get('/analytics')]
    public function index(): Response
    {
        // ...
    }
}
```

### Middleware Alias

Register an alias in `bootstrap/app.php` for cleaner syntax:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'module' => \Hunter\Module\Http\Middleware\EnsureModuleActive::class,
    ]);
})
```

Then use it as:

```php
Route::get('/analytics', AnalyticsController::class)
    ->middleware('module:hunter/analytics');
```

## How It Works

The middleware follows this verification flow:

```
1. Get authenticated user from request
   └─> If no user, return 403

2. Get tenant from user
   └─> Tries: user->tenant(), user->tenant, user->currentTenant()
   └─> If no tenant, return 403

3. Check module activation for tenant
   └─> Tries: tenant->hasModule($identifier)
   └─> Or: tenant->modules() and iterates

4. Verify module is active
   └─> Checks: pivot->is_active, pivot->activated_at, module->isActive()
   └─> If not active, return 403

5. Allow request to proceed
```

## Tenant Resolution

The middleware automatically resolves the tenant using these methods (in order):

| Method                   | Description                   |
| ------------------------ | ----------------------------- |
| `$user->tenant()`        | Method returning tenant       |
| `$user->tenant`          | Property containing tenant    |
| `$user->currentTenant()` | Method for multi-tenant users |

### Example User Model

```php
final class User extends Authenticatable
{
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
```

Or with a method:

```php
final class User extends Authenticatable
{
    public function currentTenant(): ?Tenant
    {
        return session('current_tenant_id')
            ? Tenant::find(session('current_tenant_id'))
            : $this->tenants()->first();
    }
}
```

## Module Activation Checks

The middleware supports multiple patterns for checking module activation:

### Using `hasModule()` Method

The simplest approach - implement a `hasModule()` method on your tenant:

```php
final class Tenant extends Model
{
    public function hasModule(string $identifier): bool
    {
        return $this->modules()
            ->where('identifier', $identifier)
            ->wherePivot('is_active', true)
            ->exists();
    }
}
```

### Using `modules()` Relationship

If your tenant has a `modules()` relationship, the middleware will iterate through it:

```php
final class Tenant extends Model
{
    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'tenant_modules')
            ->withPivot(['is_active', 'activated_at'])
            ->withTimestamps();
    }
}
```

### Module Identification

The middleware identifies modules by checking:

1. `$module->identifier()` method
2. `$module->identifier` property
3. `$module->module_identifier` property

### Activation Status

The middleware determines if a module is active by checking (in order):

1. `$module->pivot->is_active` - Boolean flag
2. `$module->pivot->activated_at` - Timestamp (not null = active)
3. `$module->isActive()` - Method call
4. `$module->is_active` - Property

## Error Response

When a module is not active, the middleware returns a 403 response:

```
HTTP 403 Forbidden
Module 'hunter/analytics' is not active for your organization.
```

### Customizing the Error

To customize the error response, you can create your own middleware that extends the base:

```php
final class CustomModuleMiddleware extends EnsureModuleActive
{
    public function handle(Request $request, Closure $next, string $moduleIdentifier): Response
    {
        try {
            return parent::handle($request, $next, $moduleIdentifier);
        } catch (HttpException $e) {
            if ($e->getStatusCode() === 403) {
                return Inertia::render('Errors/ModuleNotActive', [
                    'module' => $moduleIdentifier,
                ])->toResponse($request)->setStatusCode(403);
            }

            throw $e;
        }
    }
}
```

## Database Schema

A typical schema for tenant modules:

```php
// modules table
Schema::create('modules', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('identifier')->unique();  // e.g., 'hunter/analytics'
    $table->string('name');
    $table->string('version');
    $table->timestamps();
});

// tenant_modules pivot table
Schema::create('tenant_modules', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('tenant_id')->constrained()->cascadeOnDelete();
    $table->foreignUuid('module_id')->constrained()->cascadeOnDelete();
    $table->boolean('is_active')->default(true);
    $table->timestamp('activated_at')->nullable();
    $table->timestamp('deactivated_at')->nullable();
    $table->timestamps();

    $table->unique(['tenant_id', 'module_id']);
});
```

## Testing

When testing routes protected by the middleware:

```php
it('allows access when module is active', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();
    $module = Module::factory()->create(['identifier' => 'hunter/analytics']);

    $tenant->modules()->attach($module, ['is_active' => true]);

    $this->actingAs($user)
        ->get('/analytics')
        ->assertOk();
});

it('denies access when module is not active', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();
    $module = Module::factory()->create(['identifier' => 'hunter/analytics']);

    $tenant->modules()->attach($module, ['is_active' => false]);

    $this->actingAs($user)
        ->get('/analytics')
        ->assertForbidden();
});

it('denies access when module is not assigned', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();

    $this->actingAs($user)
        ->get('/analytics')
        ->assertForbidden();
});
```

---

[Previous: Navigation](03-navigation.md) | [Next: Registry](05-registry.md)
