<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use InvalidArgumentException;

final readonly class Quantity
{
    public function __construct(public int $value)
    {
        if ($value < 1) {
            throw new InvalidArgumentException('Quantity must be positive.');
        }
    }
}
