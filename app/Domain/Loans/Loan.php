<?php

declare(strict_types=1);

namespace App\Domain\Loans;

use App\Domain\Shared\Isbn;
use DateInterval;
use DateTimeImmutable;
use DomainException;

final class Loan
{
    /** @var array<string, LoanItem> */
    private array $items = [];

    public function __construct(
        public readonly string $id,
        public readonly string $memberId,
        public readonly string $loanedByUserId,
        public readonly DateTimeImmutable $loanedAt,
    ) {
    }

    /** @return list<LoanItem> */
    public function items(): array
    {
        return array_values($this->items);
    }

    public function addItem(
        string $id,
        string $bookId,
        string $bookTitle,
        Isbn $isbn,
        string $categoryName,
    ): LoanItem {
        if (isset($this->items[$bookId])) {
            throw new DomainException('A book cannot appear twice in the same loan.');
        }

        if (count($this->items) === 3) {
            throw new DomainException('A loan cannot contain more than three items.');
        }

        $item = new LoanItem(
            id: $id,
            bookId: $bookId,
            bookTitle: $bookTitle,
            isbn: $isbn,
            categoryName: $categoryName,
            dueAt: $this->loanedAt->add(new DateInterval('P14D')),
        );
        $this->items[$bookId] = $item;

        return $item;
    }

    public function returnItem(string $itemId, DateTimeImmutable $at): void
    {
        foreach ($this->items as $item) {
            if ($item->id === $itemId) {
                $item->returnAt($at);

                return;
            }
        }

        throw new DomainException('Loan item does not belong to this loan.');
    }

    public function statusAt(DateTimeImmutable $now): LoanStatus
    {
        $allReturned = $this->items !== [];

        foreach ($this->items as $item) {
            $allReturned = $allReturned && $item->returnedAt() !== null;
        }

        if ($allReturned) {
            return LoanStatus::Closed;
        }

        foreach ($this->items as $item) {
            if ($item->returnedAt() === null && $item->dueAt < $now) {
                return LoanStatus::Overdue;
            }
        }

        return LoanStatus::Open;
    }
}
