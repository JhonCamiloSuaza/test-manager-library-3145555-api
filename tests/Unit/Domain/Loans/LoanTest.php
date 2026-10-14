<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Loans;

use App\Domain\Loans\Loan;
use App\Domain\Loans\LoanStatus;
use App\Domain\Shared\Isbn;
use DateTimeImmutable;
use DomainException;
use PHPUnit\Framework\TestCase;

final class LoanTest extends TestCase
{
    public function test_item_due_date_is_fourteen_days_after_lending(): void
    {
        $loan = $this->loan();
        $item = $this->addItem($loan, 'item-1', 'book-1');

        self::assertSame('2026-10-15T10:00:00+00:00', $item->dueAt->format(DATE_ATOM));
    }

    public function test_loan_rejects_duplicate_books_and_more_than_three_items(): void
    {
        $loan = $this->loan();
        $this->addItem($loan, 'item-1', 'book-1');

        $this->expectException(DomainException::class);
        $this->addItem($loan, 'item-2', 'book-1');
    }

    public function test_loan_status_is_calculated_and_return_is_not_repeatable(): void
    {
        $loan = $this->loan();
        $item = $this->addItem($loan, 'item-1', 'book-1');

        self::assertSame(LoanStatus::Open, $loan->statusAt(new DateTimeImmutable('2026-10-02T10:00:00+00:00')));
        self::assertSame(LoanStatus::Overdue, $loan->statusAt(new DateTimeImmutable('2026-10-16T10:00:00+00:00')));

        $loan->returnItem($item->id, new DateTimeImmutable('2026-10-03T10:00:00+00:00'));
        self::assertSame(LoanStatus::Closed, $loan->statusAt(new DateTimeImmutable('2026-10-16T10:00:00+00:00')));

        $this->expectException(DomainException::class);
        $loan->returnItem($item->id, new DateTimeImmutable('2026-10-04T10:00:00+00:00'));
    }

    private function loan(): Loan
    {
        return new Loan('loan-1', 'member-1', 'user-1', new DateTimeImmutable('2026-10-01T10:00:00+00:00'));
    }

    private function addItem(Loan $loan, string $id, string $bookId): mixed
    {
        return $loan->addItem($id, $bookId, 'Book title', new Isbn('9780307474728'), 'Tecnología');
    }
}
