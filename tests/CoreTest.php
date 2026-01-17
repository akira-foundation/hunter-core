<?php

declare(strict_types=1);

use Core\Core\Core;

it('can be instantiated', function (): void {
    $core = new Core();

    expect($core)->toBeInstanceOf(Core::class);
});
