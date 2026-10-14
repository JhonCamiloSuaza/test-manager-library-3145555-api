<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use InvalidArgumentException;

final readonly class Isbn
{
    public function __construct(public string $value)
    {
        if (preg_match('/^\d{13}$/D', $value) !== 1) {
            throw new InvalidArgumentException('ISBN must contain exactly 13 digits.');
        }
    }
}
