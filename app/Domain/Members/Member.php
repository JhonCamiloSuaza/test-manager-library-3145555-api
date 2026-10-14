<?php

declare(strict_types=1);

namespace App\Domain\Members;

use App\Domain\Shared\DocumentNumber;

final readonly class Member
{
    public function __construct(
        public string $id,
        public string $fullName,
        public DocumentNumber $documentNumber,
    ) {
    }
}
