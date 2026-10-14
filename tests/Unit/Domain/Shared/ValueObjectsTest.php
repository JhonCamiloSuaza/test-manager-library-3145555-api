<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use App\Domain\Shared\DateRange;
use App\Domain\Shared\DocumentNumber;
use App\Domain\Shared\Isbn;
use App\Domain\Shared\Quantity;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ValueObjectsTest extends TestCase
{
    public function test_isbn_accepts_exactly_thirteen_digits(): void
    {
        self::assertSame('9780307474728', (new Isbn('9780307474728'))->value);
    }

    public function test_isbn_rejects_non_thirteen_digit_values(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Isbn('978030747472X');
    }

    public function test_quantity_must_be_positive(): void
    {
        self::assertSame(1, (new Quantity(1))->value);

        $this->expectException(InvalidArgumentException::class);
        new Quantity(0);
    }

    public function test_document_number_is_required_and_trimmed(): void
    {
        self::assertSame('12345', (new DocumentNumber(' 12345 '))->value);

        $this->expectException(InvalidArgumentException::class);
        new DocumentNumber('   ');
    }

    public function test_date_range_cannot_be_inverted(): void
    {
        $from = new DateTimeImmutable('2026-10-01T00:00:00+00:00');
        $to = new DateTimeImmutable('2026-10-31T00:00:00+00:00');

        self::assertSame($from, (new DateRange($from, $to))->from);

        $this->expectException(InvalidArgumentException::class);
        new DateRange($to, $from);
    }
}
