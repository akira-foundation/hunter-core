<?php

declare(strict_types=1);

use Hunter\Module\Enums\ModuleLog;
use Hunter\Module\Enums\ModuleLogStatus;
use Hunter\Module\Enums\ModuleStatus;

describe('ModuleStatus', function (): void {
    it('has all expected cases', function (): void {
        expect(ModuleStatus::cases())->toHaveCount(6)
            ->and(ModuleStatus::Pending->value)->toBe('pending')
            ->and(ModuleStatus::Installing->value)->toBe('installing')
            ->and(ModuleStatus::Installed->value)->toBe('installed')
            ->and(ModuleStatus::Failed->value)->toBe('failed')
            ->and(ModuleStatus::Updating->value)->toBe('updating')
            ->and(ModuleStatus::Removing->value)->toBe('removing');
    });

    it('returns correct labels for all cases', function (): void {
        expect(ModuleStatus::Pending->label())->toBe('Pending')
            ->and(ModuleStatus::Installing->label())->toBe('Installing')
            ->and(ModuleStatus::Installed->label())->toBe('Installed')
            ->and(ModuleStatus::Failed->label())->toBe('Failed')
            ->and(ModuleStatus::Updating->label())->toBe('Updating')
            ->and(ModuleStatus::Removing->label())->toBe('Removing');
    });

    it('identifies non-operational statuses', function (): void {
        expect(ModuleStatus::Failed->isOperational())->toBeFalse()
            ->and(ModuleStatus::Updating->isOperational())->toBeFalse()
            ->and(ModuleStatus::Removing->isOperational())->toBeFalse();
    });

    it('identifies non-transitional statuses', function (): void {
        expect(ModuleStatus::Failed->isTransitional())->toBeFalse();
    });

    it('identifies operational status', function (): void {
        expect(ModuleStatus::Installed->isOperational())->toBeTrue()
            ->and(ModuleStatus::Pending->isOperational())->toBeFalse()
            ->and(ModuleStatus::Installing->isOperational())->toBeFalse();
    });

    it('identifies transitional statuses', function (): void {
        expect(ModuleStatus::Installing->isTransitional())->toBeTrue()
            ->and(ModuleStatus::Updating->isTransitional())->toBeTrue()
            ->and(ModuleStatus::Removing->isTransitional())->toBeTrue()
            ->and(ModuleStatus::Installed->isTransitional())->toBeFalse()
            ->and(ModuleStatus::Pending->isTransitional())->toBeFalse();
    });
});

describe('ModuleLogAction', function (): void {
    it('has all expected cases', function (): void {
        expect(ModuleLog::cases())->toHaveCount(8)
            ->and(ModuleLog::Install->value)->toBe('install')
            ->and(ModuleLog::Update->value)->toBe('update')
            ->and(ModuleLog::Remove->value)->toBe('remove')
            ->and(ModuleLog::Activate->value)->toBe('activate')
            ->and(ModuleLog::Deactivate->value)->toBe('deactivate');
    });

    it('returns correct labels for all cases', function (): void {
        expect(ModuleLog::Install->label())->toBe('Install')
            ->and(ModuleLog::Update->label())->toBe('Update')
            ->and(ModuleLog::Remove->label())->toBe('Remove')
            ->and(ModuleLog::Publish->label())->toBe('Publish')
            ->and(ModuleLog::Migrate->label())->toBe('Migrate')
            ->and(ModuleLog::Build->label())->toBe('Build')
            ->and(ModuleLog::Activate->label())->toBe('Activate')
            ->and(ModuleLog::Deactivate->label())->toBe('Deactivate');
    });

    it('returns correct verbs for all cases', function (): void {
        expect(ModuleLog::Install->verb())->toBe('installed')
            ->and(ModuleLog::Update->verb())->toBe('updated')
            ->and(ModuleLog::Remove->verb())->toBe('removed')
            ->and(ModuleLog::Publish->verb())->toBe('published')
            ->and(ModuleLog::Migrate->verb())->toBe('migrated')
            ->and(ModuleLog::Build->verb())->toBe('built')
            ->and(ModuleLog::Activate->verb())->toBe('activated')
            ->and(ModuleLog::Deactivate->verb())->toBe('deactivated');
    });
});

describe('ModuleLogStatus', function (): void {
    it('has all expected cases', function (): void {
        expect(ModuleLogStatus::cases())->toHaveCount(3)
            ->and(ModuleLogStatus::Started->value)->toBe('started')
            ->and(ModuleLogStatus::Completed->value)->toBe('completed')
            ->and(ModuleLogStatus::Failed->value)->toBe('failed');
    });

    it('returns correct labels for all cases', function (): void {
        expect(ModuleLogStatus::Started->label())->toBe('Started')
            ->and(ModuleLogStatus::Completed->label())->toBe('Completed')
            ->and(ModuleLogStatus::Failed->label())->toBe('Failed');
    });

    it('identifies terminal statuses', function (): void {
        expect(ModuleLogStatus::Completed->isTerminal())->toBeTrue()
            ->and(ModuleLogStatus::Failed->isTerminal())->toBeTrue()
            ->and(ModuleLogStatus::Started->isTerminal())->toBeFalse();
    });
});
