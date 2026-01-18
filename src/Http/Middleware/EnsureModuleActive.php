<?php

declare(strict_types=1);

namespace Hunter\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use function is_object;

final class EnsureModuleActive
{
    /**
     * @param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next, string $moduleIdentifier): Response
    {
        if (! $this->isModuleActiveForTenant($request, $moduleIdentifier)) {
            abort(403, "Module '{$moduleIdentifier}' is not active for your organization.");
        }

        return $next($request);
    }

    private function isModuleActiveForTenant(Request $request, string $moduleIdentifier): bool
    {
        $user = $request->user();

        if ($user === null) {
            return false;
        }

        $tenant = $this->getTenantFromUser($user);

        if ($tenant === null) {
            return false;
        }

        return $this->checkTenantModuleActivation($tenant, $moduleIdentifier);
    }

    private function getTenantFromUser(mixed $user): ?object
    {
        if (method_exists($user, 'tenant')) {
            return $user->tenant();
        }

        if (property_exists($user, 'tenant')) {
            return $user->tenant;
        }

        if (method_exists($user, 'currentTenant')) {
            return $user->currentTenant();
        }

        return null;
    }

    private function checkTenantModuleActivation(object $tenant, string $moduleIdentifier): bool
    {
        if (method_exists($tenant, 'hasModule')) {
            return $tenant->hasModule($moduleIdentifier);
        }

        if (method_exists($tenant, 'modules')) {
            $modules = $tenant->modules();

            if (is_iterable($modules)) {
                foreach ($modules as $module) {
                    $identifier = $this->getModuleIdentifier($module);

                    if ($identifier === $moduleIdentifier) {
                        return $this->isModuleActivated($module);
                    }
                }
            }
        }

        return false;
    }

    private function getModuleIdentifier(mixed $module): ?string
    {
        if (is_object($module)) {
            if (method_exists($module, 'identifier')) {
                return $module->identifier();
            }

            if (property_exists($module, 'identifier')) {
                return $module->identifier;
            }

            if (property_exists($module, 'module_identifier')) {
                return $module->module_identifier;
            }
        }

        return null;
    }

    private function isModuleActivated(mixed $module): bool
    {
        if (is_object($module)) {
            if (property_exists($module, 'pivot') && is_object($module->pivot)) {
                $pivot = $module->pivot;

                if (property_exists($pivot, 'is_active')) {
                    return (bool) $pivot->is_active;
                }

                if (property_exists($pivot, 'activated_at')) {
                    return $pivot->activated_at !== null;
                }
            }

            if (method_exists($module, 'isActive')) {
                return $module->isActive();
            }

            if (property_exists($module, 'is_active')) {
                return (bool) $module->is_active;
            }
        }

        return true;
    }
}
