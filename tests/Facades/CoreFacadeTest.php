<?php

declare(strict_types=1);

use Hunter\Core\Core;
use Hunter\Core\Facades\Core as CoreFacade;

it('resolves to the Core class', function (): void {
    expect(CoreFacade::getFacadeRoot())->toBeInstanceOf(Core::class);
});
