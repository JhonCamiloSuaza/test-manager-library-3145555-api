<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use InvalidArgumentException;

final readonly class DocumentNumber
{
    public string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidArgumentException('Document number is required.');
        }

        $this->value = $value;
    }
}
