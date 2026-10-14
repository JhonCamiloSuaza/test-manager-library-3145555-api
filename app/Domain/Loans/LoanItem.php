<?php

declare(strict_types=1);

namespace App\Domain\Loans;

use App\Domain\Shared\Isbn;
use DateTimeImmutable;
use DomainException;

final class LoanItem
{
    private ?DateTimeImmutable $returnedAt = null;

    public function __construct(
        public readonly string $id,
        public readonly string $bookId,
        public readonly string $bookTitle,
        public readonly Isbn $isbn,
        public readonly string $categoryName,
        public readonly DateTimeImmutable $dueAt,
    ) {
    }

    public function returnedAt(): ?DateTimeImmutable
    {
        return $this->returnedAt;
    }

    public function returnAt(DateTimeImmutable $at): void
    {
        if ($this->returnedAt !== null) {
            throw new DomainException('Loan item has already been returned.');
        }

        $this->returnedAt = $at;
    }
}
