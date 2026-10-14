<?php

declare(strict_types=1);

namespace App\Domain\Books;

use App\Domain\Shared\Isbn;
use DateTimeImmutable;
use DomainException;

final class Book
{
    private ?DateTimeImmutable $deletedAt;

    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly string $author,
        public readonly Isbn $isbn,
        public readonly string $categoryId,
        private int $totalCopies,
        private int $availableCopies,
        public readonly ?string $coverKey,
        ?DateTimeImmutable $deletedAt = null,
    ) {
        $this->assertInventoryIsValid($totalCopies, $availableCopies);
        $this->deletedAt = $deletedAt;
    }

    public function totalCopies(): int
    {
        return $this->totalCopies;
    }

    public function availableCopies(): int
    {
        return $this->availableCopies;
    }

    public function deletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function withdraw(): void
    {
        if ($this->deletedAt !== null) {
            throw new DomainException('A logically deleted book cannot be withdrawn.');
        }

        if ($this->availableCopies === 0) {
            throw new DomainException('Book is not available.');
        }

        $this->availableCopies--;
    }

    public function restock(): void
    {
        if ($this->availableCopies === $this->totalCopies) {
            throw new DomainException('Book availability cannot exceed total copies.');
        }

        $this->availableCopies++;
    }

    public function adjustTotalCopies(int $totalCopies): void
    {
        $availableCopies = $this->availableCopies + ($totalCopies - $this->totalCopies);
        $this->assertInventoryIsValid($totalCopies, $availableCopies);

        $this->totalCopies = $totalCopies;
        $this->availableCopies = $availableCopies;
    }

    public function markDeleted(DateTimeImmutable $at): void
    {
        $this->deletedAt = $at;
    }

    private function assertInventoryIsValid(int $totalCopies, int $availableCopies): void
    {
        if ($totalCopies < 1 || $availableCopies < 0 || $availableCopies > $totalCopies) {
            throw new DomainException('Book inventory is outside its allowed range.');
        }
    }
}
