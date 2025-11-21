<?php

declare(strict_types=1);

namespace AppBundle\Security;

use AppBundle\Association\Model\User;
use AppBundle\Event\Model\GithubUser;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class UserFinder
{
    public function __construct(
        private Security $security,
    ) {}

    public function currentUserId(): ?int
    {
        $user = $this->security->getUser();

        if ($user instanceof User || $user instanceof GithubUser) {
            return $user->getId();
        }

        return null;
    }

    public function currentUser(): User|GithubUser|null
    {
        $user = $this->security->getUser();

        if ($user === null) {
            return null;
        }

        if (!$user instanceof User && !$user instanceof GithubUser) {
            throw new \Exception();
        }

        return $user;
    }

    public function currentDatabaseUser(): ?User
    {
        $user = $this->security->getUser();

        if ($user !== null && !$user instanceof User) {
            throw new \Exception();
        }

        return $user;
    }

    public function currentGithubUser(): ?GithubUser
    {
        $user = $this->security->getUser();

        if ($user !== null && !$user instanceof GithubUser) {
            throw new \Exception();
        }

        return $user;
    }
}
