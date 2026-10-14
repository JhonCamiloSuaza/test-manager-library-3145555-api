<?php

declare(strict_types=1);

namespace App\Domain\Users;

use InvalidArgumentException;

final readonly class User
{
    private function __construct(
        public string $id,
        public string $username,
        public string $passwordHash,
        public UserRole $role,
    ) {
    }

    public static function librarian(string $id, string $username, string $passwordHash): self
    {
        return new self($id, self::normalizeUsername($username), $passwordHash, UserRole::Librarian);
    }

    public static function administrator(string $id, string $username, string $passwordHash): self
    {
        return new self($id, self::normalizeUsername($username), $passwordHash, UserRole::Admin);
    }

    private static function normalizeUsername(string $username): string
    {
        $username = strtolower(trim($username));

        if ($username === '') {
            throw new InvalidArgumentException('Username is required.');
        }

        return $username;
    }
}
