<?php

declare(strict_types=1);

use Hunter\Core\Http\Middleware\EnsureModuleActive;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

describe('EnsureModuleActive middleware', function (): void {
    it('aborts with 403 when user is not authenticated', function (): void {
        $middleware = new EnsureModuleActive();
        $request    = Request::create('/test');

        $middleware->handle($request, fn () => response('ok'), 'hunter/analytics');
    })->throws(HttpException::class);

    it('aborts with 403 when user has no tenant method', function (): void {
        $middleware = new EnsureModuleActive();
        $request    = Request::create('/test');

        $user = new class ()
        {
            public string $name = 'Test User';
        };

        $request->setUserResolver(fn () => $user);

        $middleware->handle($request, fn () => response('ok'), 'hunter/analytics');
    })->throws(HttpException::class);

    it('aborts with 403 when tenant is null from tenant method', function (): void {
        $middleware = new EnsureModuleActive();
        $request    = Request::create('/test');

        $user = new class ()
        {
            public function tenant(): ?object
            {
                return null;
            }
        };

        $request->setUserResolver(fn () => $user);

        $middleware->handle($request, fn () => response('ok'), 'hunter/analytics');
    })->throws(HttpException::class);

    it('aborts with 403 when tenant is null from tenant property', function (): void {
        $middleware = new EnsureModuleActive();
        $request    = Request::create('/test');

        $user = new class ()
        {
            public ?object $tenant = null;
        };

        $request->setUserResolver(fn () => $user);

        $middleware->handle($request, fn () => response('ok'), 'hunter/analytics');
    })->throws(HttpException::class);

    it('aborts with 403 when tenant is null from currentTenant method', function (): void {
        $middleware = new EnsureModuleActive();
        $request    = Request::create('/test');

        $user = new class ()
        {
            public function currentTenant(): ?object
            {
                return null;
            }
        };

        $request->setUserResolver(fn () => $user);

        $middleware->handle($request, fn () => response('ok'), 'hunter/analytics');
    })->throws(HttpException::class);

    it('passes when tenant hasModule returns true', function (): void {
        $middleware = new EnsureModuleActive();
        $request    = Request::create('/test');

        $tenant = new class ()
        {
            public function hasModule(string $identifier): bool
            {
                return $identifier === 'hunter/analytics';
            }
        };

        $user = new class ($tenant)
        {
            public function __construct(public object $tenantObj) {}

            public function tenant(): object
            {
                return $this->tenantObj;
            }
        };

        $request->setUserResolver(fn () => $user);

        $response = $middleware->handle($request, fn () => response('ok'), 'hunter/analytics');

        expect($response->getContent())->toBe('ok');
    });

    it('aborts when tenant hasModule returns false', function (): void {
        $middleware = new EnsureModuleActive();
        $request    = Request::create('/test');

        $tenant = new class ()
        {
            public function hasModule(string $identifier): bool
            {
                return false;
            }
        };

        $user = new class ($tenant)
        {
            public function __construct(public object $tenantObj) {}

            public function tenant(): object
            {
                return $this->tenantObj;
            }
        };

        $request->setUserResolver(fn () => $user);

        $middleware->handle($request, fn () => response('ok'), 'hunter/analytics');
    })->throws(HttpException::class);

    it('passes when module found in tenant modules with identifier method', function (): void {
        $middleware = new EnsureModuleActive();
        $request    = Request::create('/test');

        $module = new class ()
        {
            public function identifier(): string
            {
                return 'hunter/analytics';
            }

            public function isActive(): bool
            {
                return true;
            }
        };

        $tenant = new class ($module)
        {
            public function __construct(public object $moduleObj) {}

            public function modules(): array
            {
                return [$this->moduleObj];
            }
        };

        $user = new class ($tenant)
        {
            public function __construct(public object $tenantObj) {}

            public function tenant(): object
            {
                return $this->tenantObj;
            }
        };

        $request->setUserResolver(fn () => $user);

        $response = $middleware->handle($request, fn () => response('ok'), 'hunter/analytics');

        expect($response->getContent())->toBe('ok');
    });

    it('passes when module found with identifier property', function (): void {
        $middleware = new EnsureModuleActive();
        $request    = Request::create('/test');

        $module = new class ()
        {
            public string $identifier = 'hunter/analytics';

            public bool $is_active = true;
        };

        $tenant = new class ($module)
        {
            public function __construct(public object $moduleObj) {}

            public function modules(): array
            {
                return [$this->moduleObj];
            }
        };

        $user = new class ($tenant)
        {
            public function __construct(public object $tenantObj) {}

            public function tenant(): object
            {
                return $this->tenantObj;
            }
        };

        $request->setUserResolver(fn () => $user);

        $response = $middleware->handle($request, fn () => response('ok'), 'hunter/analytics');

        expect($response->getContent())->toBe('ok');
    });

    it('passes when module found with module_identifier property', function (): void {
        $middleware = new EnsureModuleActive();
        $request    = Request::create('/test');

        $module = new class ()
        {
            public string $module_identifier = 'hunter/analytics';
        };

        $tenant = new class ($module)
        {
            public function __construct(public object $moduleObj) {}

            public function modules(): array
            {
                return [$this->moduleObj];
            }
        };

        $user = new class ($tenant)
        {
            public function __construct(public object $tenantObj) {}

            public function tenant(): object
            {
                return $this->tenantObj;
            }
        };

        $request->setUserResolver(fn () => $user);

        $response = $middleware->handle($request, fn () => response('ok'), 'hunter/analytics');

        expect($response->getContent())->toBe('ok');
    });

    it('aborts when module identifier does not match', function (): void {
        $middleware = new EnsureModuleActive();
        $request    = Request::create('/test');

        $module = new class ()
        {
            public function identifier(): string
            {
                return 'hunter/crm';
            }
        };

        $tenant = new class ($module)
        {
            public function __construct(public object $moduleObj) {}

            public function modules(): array
            {
                return [$this->moduleObj];
            }
        };

        $user = new class ($tenant)
        {
            public function __construct(public object $tenantObj) {}

            public function tenant(): object
            {
                return $this->tenantObj;
            }
        };

        $request->setUserResolver(fn () => $user);

        $middleware->handle($request, fn () => response('ok'), 'hunter/analytics');
    })->throws(HttpException::class);

    it('checks pivot is_active property', function (): void {
        $middleware = new EnsureModuleActive();
        $request    = Request::create('/test');

        $pivot = new class ()
        {
            public bool $is_active = true;
        };

        $module = new class ($pivot)
        {
            public function __construct(public object $pivot) {}

            public function identifier(): string
            {
                return 'hunter/analytics';
            }
        };

        $tenant = new class ($module)
        {
            public function __construct(public object $moduleObj) {}

            public function modules(): array
            {
                return [$this->moduleObj];
            }
        };

        $user = new class ($tenant)
        {
            public function __construct(public object $tenantObj) {}

            public function tenant(): object
            {
                return $this->tenantObj;
            }
        };

        $request->setUserResolver(fn () => $user);

        $response = $middleware->handle($request, fn () => response('ok'), 'hunter/analytics');

        expect($response->getContent())->toBe('ok');
    });

    it('aborts when pivot is_active is false', function (): void {
        $middleware = new EnsureModuleActive();
        $request    = Request::create('/test');

        $pivot = new class ()
        {
            public bool $is_active = false;
        };

        $module = new class ($pivot)
        {
            public function __construct(public object $pivot) {}

            public function identifier(): string
            {
                return 'hunter/analytics';
            }
        };

        $tenant = new class ($module)
        {
            public function __construct(public object $moduleObj) {}

            public function modules(): array
            {
                return [$this->moduleObj];
            }
        };

        $user = new class ($tenant)
        {
            public function __construct(public object $tenantObj) {}

            public function tenant(): object
            {
                return $this->tenantObj;
            }
        };

        $request->setUserResolver(fn () => $user);

        $middleware->handle($request, fn () => response('ok'), 'hunter/analytics');
    })->throws(HttpException::class);

    it('checks pivot activated_at property', function (): void {
        $middleware = new EnsureModuleActive();
        $request    = Request::create('/test');

        $pivot = new class ()
        {
            public ?string $activated_at = '2024-01-01 00:00:00';
        };

        $module = new class ($pivot)
        {
            public function __construct(public object $pivot) {}

            public function identifier(): string
            {
                return 'hunter/analytics';
            }
        };

        $tenant = new class ($module)
        {
            public function __construct(public object $moduleObj) {}

            public function modules(): array
            {
                return [$this->moduleObj];
            }
        };

        $user = new class ($tenant)
        {
            public function __construct(public object $tenantObj) {}

            public function tenant(): object
            {
                return $this->tenantObj;
            }
        };

        $request->setUserResolver(fn () => $user);

        $response = $middleware->handle($request, fn () => response('ok'), 'hunter/analytics');

        expect($response->getContent())->toBe('ok');
    });

    it('aborts when pivot activated_at is null', function (): void {
        $middleware = new EnsureModuleActive();
        $request    = Request::create('/test');

        $pivot = new class ()
        {
            public ?string $activated_at = null;
        };

        $module = new class ($pivot)
        {
            public function __construct(public object $pivot) {}

            public function identifier(): string
            {
                return 'hunter/analytics';
            }
        };

        $tenant = new class ($module)
        {
            public function __construct(public object $moduleObj) {}

            public function modules(): array
            {
                return [$this->moduleObj];
            }
        };

        $user = new class ($tenant)
        {
            public function __construct(public object $tenantObj) {}

            public function tenant(): object
            {
                return $this->tenantObj;
            }
        };

        $request->setUserResolver(fn () => $user);

        $middleware->handle($request, fn () => response('ok'), 'hunter/analytics');
    })->throws(HttpException::class);

    it('gets tenant from tenant property', function (): void {
        $middleware = new EnsureModuleActive();
        $request    = Request::create('/test');

        $tenant = new class ()
        {
            public function hasModule(string $identifier): bool
            {
                return true;
            }
        };

        $user = new class ($tenant)
        {
            public function __construct(public object $tenant) {}
        };

        $request->setUserResolver(fn () => $user);

        $response = $middleware->handle($request, fn () => response('ok'), 'hunter/analytics');

        expect($response->getContent())->toBe('ok');
    });

    it('gets tenant from currentTenant method', function (): void {
        $middleware = new EnsureModuleActive();
        $request    = Request::create('/test');

        $tenant = new class ()
        {
            public function hasModule(string $identifier): bool
            {
                return true;
            }
        };

        $user = new class ($tenant)
        {
            public function __construct(private object $tenantObj) {}

            public function currentTenant(): object
            {
                return $this->tenantObj;
            }
        };

        $request->setUserResolver(fn () => $user);

        $response = $middleware->handle($request, fn () => response('ok'), 'hunter/analytics');

        expect($response->getContent())->toBe('ok');
    });

    it('aborts when tenant has no modules method and no hasModule', function (): void {
        $middleware = new EnsureModuleActive();
        $request    = Request::create('/test');

        $tenant = new class () {};

        $user = new class ($tenant)
        {
            public function __construct(public object $tenant) {}
        };

        $request->setUserResolver(fn () => $user);

        $middleware->handle($request, fn () => response('ok'), 'hunter/analytics');
    })->throws(HttpException::class);

    it('handles non-iterable modules return value', function (): void {
        $middleware = new EnsureModuleActive();
        $request    = Request::create('/test');

        $tenant = new class ()
        {
            public function modules(): string
            {
                return 'not iterable';
            }
        };

        $user = new class ($tenant)
        {
            public function __construct(public object $tenant) {}
        };

        $request->setUserResolver(fn () => $user);

        $middleware->handle($request, fn () => response('ok'), 'hunter/analytics');
    })->throws(HttpException::class);

    it('handles module without identifier', function (): void {
        $middleware = new EnsureModuleActive();
        $request    = Request::create('/test');

        $module = new class ()
        {
            public string $name = 'some module';
        };

        $tenant = new class ($module)
        {
            public function __construct(public object $moduleObj) {}

            public function modules(): array
            {
                return [$this->moduleObj];
            }
        };

        $user = new class ($tenant)
        {
            public function __construct(public object $tenant) {}
        };

        $request->setUserResolver(fn () => $user);

        $middleware->handle($request, fn () => response('ok'), 'hunter/analytics');
    })->throws(HttpException::class);

    it('handles non-object module in modules array', function (): void {
        $middleware = new EnsureModuleActive();
        $request    = Request::create('/test');

        $tenant = new class ()
        {
            public function modules(): array
            {
                return ['string-module', 123, null];
            }
        };

        $user = new class ($tenant)
        {
            public function __construct(public object $tenant) {}
        };

        $request->setUserResolver(fn () => $user);

        $middleware->handle($request, fn () => response('ok'), 'hunter/analytics');
    })->throws(HttpException::class);

    it('handles non-object in isModuleActivated', function (): void {
        $middleware = new EnsureModuleActive();
        $request    = Request::create('/test');

        $module = new class ()
        {
            public function identifier(): string
            {
                return 'hunter/analytics';
            }
        };

        $tenant = new class ($module)
        {
            public function __construct(public object $moduleObj) {}

            public function modules(): array
            {
                return [$this->moduleObj];
            }
        };

        $user = new class ($tenant)
        {
            public function __construct(public object $tenant) {}
        };

        $request->setUserResolver(fn () => $user);

        $response = $middleware->handle($request, fn () => response('ok'), 'hunter/analytics');

        expect($response->getContent())->toBe('ok');
    });
});
