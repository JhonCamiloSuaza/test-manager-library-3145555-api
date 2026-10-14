<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use DateTimeImmutable;
use InvalidArgumentException;

final readonly class DateRange
{
    public function __construct(
        public DateTimeImmutable $from,
        public DateTimeImmutable $to,
    ) {
        if ($to < $from) {
            throw new InvalidArgumentException('End date cannot be before start date.');
        }
    }
}
