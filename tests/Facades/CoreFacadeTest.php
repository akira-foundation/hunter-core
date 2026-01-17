<?php

declare(strict_types=1);

use Core\Core\Core;
use Core\Core\Facades\Core as CoreFacade;

it('resolves to the Core class', function (): void {
    expect(CoreFacade::getFacadeRoot())->toBeInstanceOf(Core::class);
});
