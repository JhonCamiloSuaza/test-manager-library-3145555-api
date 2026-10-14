<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Books;

use App\Domain\Books\Book;
use App\Domain\Shared\Isbn;
use DateTimeImmutable;
use DomainException;
use PHPUnit\Framework\TestCase;

final class BookTest extends TestCase
{
    public function test_withdraw_and_restock_keep_availability_within_range(): void
    {
        $book = $this->book(totalCopies: 2, availableCopies: 1);

        $book->withdraw();
        self::assertSame(0, $book->availableCopies());

        $book->restock();
        self::assertSame(1, $book->availableCopies());
    }

    public function test_withdraw_rejects_an_unavailable_book(): void
    {
        $this->expectException(DomainException::class);

        $this->book(totalCopies: 1, availableCopies: 0)->withdraw();
    }

    public function test_restock_rejects_availability_above_total(): void
    {
        $this->expectException(DomainException::class);

        $this->book(totalCopies: 1, availableCopies: 1)->restock();
    }

    public function test_total_adjustment_preserves_the_borrowed_copy_count(): void
    {
        $book = $this->book(totalCopies: 3, availableCopies: 1);

        $book->adjustTotalCopies(2);

        self::assertSame(2, $book->totalCopies());
        self::assertSame(0, $book->availableCopies());
    }

    public function test_total_adjustment_rejects_an_invalid_inventory_range(): void
    {
        $this->expectException(DomainException::class);

        $this->book(totalCopies: 3, availableCopies: 1)->adjustTotalCopies(1);
    }

    public function test_logical_deletion_keeps_the_book_as_a_domain_record(): void
    {
        $at = new DateTimeImmutable('2026-10-14T12:00:00+00:00');
        $book = $this->book(totalCopies: 1, availableCopies: 1);

        $book->markDeleted($at);

        self::assertSame($at, $book->deletedAt());
    }

    private function book(int $totalCopies, int $availableCopies): Book
    {
        return new Book(
            id: '8a2aa1b0-388e-4c37-9d64-69b04cb466dd',
            title: 'Domain-Driven Design',
            author: 'Eric Evans',
            isbn: new Isbn('9780307474728'),
            categoryId: '33333333-3333-4333-8333-333333333333',
            totalCopies: $totalCopies,
            availableCopies: $availableCopies,
            coverKey: null,
        );
    }
}
