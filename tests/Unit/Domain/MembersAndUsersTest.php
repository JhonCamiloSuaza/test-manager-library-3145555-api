<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Domain\Members\Member;
use App\Domain\Shared\DocumentNumber;
use App\Domain\Users\User;
use App\Domain\Users\UserRole;
use PHPUnit\Framework\TestCase;

final class MembersAndUsersTest extends TestCase
{
    public function test_member_keeps_the_required_document_number(): void
    {
        $member = new Member('member-1', 'Ana', new DocumentNumber(' 12345 '));

        self::assertSame('12345', $member->documentNumber->value);
    }

    public function test_librarian_username_is_trimmed_and_lowercased(): void
    {
        $user = User::librarian('user-1', ' Ana ', 'hash');

        self::assertSame('ana', $user->username);
        self::assertSame(UserRole::Librarian, $user->role);
    }

    public function test_api_creation_has_a_librarian_only_factory(): void
    {
        $user = User::librarian('user-1', 'librarian', 'hash');

        self::assertNotSame(UserRole::Admin, $user->role);
    }
}
