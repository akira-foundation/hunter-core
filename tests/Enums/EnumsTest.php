<?php

declare(strict_types=1);

use Hunter\Core\Enums\ModuleLogAction;
use Hunter\Core\Enums\ModuleLogStatus;
use Hunter\Core\Enums\ModuleStatus;

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
        expect(ModuleLogAction::cases())->toHaveCount(8)
            ->and(ModuleLogAction::Install->value)->toBe('install')
            ->and(ModuleLogAction::Update->value)->toBe('update')
            ->and(ModuleLogAction::Remove->value)->toBe('remove')
            ->and(ModuleLogAction::Activate->value)->toBe('activate')
            ->and(ModuleLogAction::Deactivate->value)->toBe('deactivate');
    });

    it('returns correct labels for all cases', function (): void {
        expect(ModuleLogAction::Install->label())->toBe('Install')
            ->and(ModuleLogAction::Update->label())->toBe('Update')
            ->and(ModuleLogAction::Remove->label())->toBe('Remove')
            ->and(ModuleLogAction::Publish->label())->toBe('Publish')
            ->and(ModuleLogAction::Migrate->label())->toBe('Migrate')
            ->and(ModuleLogAction::Build->label())->toBe('Build')
            ->and(ModuleLogAction::Activate->label())->toBe('Activate')
            ->and(ModuleLogAction::Deactivate->label())->toBe('Deactivate');
    });

    it('returns correct verbs for all cases', function (): void {
        expect(ModuleLogAction::Install->verb())->toBe('installed')
            ->and(ModuleLogAction::Update->verb())->toBe('updated')
            ->and(ModuleLogAction::Remove->verb())->toBe('removed')
            ->and(ModuleLogAction::Publish->verb())->toBe('published')
            ->and(ModuleLogAction::Migrate->verb())->toBe('migrated')
            ->and(ModuleLogAction::Build->verb())->toBe('built')
            ->and(ModuleLogAction::Activate->verb())->toBe('activated')
            ->and(ModuleLogAction::Deactivate->verb())->toBe('deactivated');
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
