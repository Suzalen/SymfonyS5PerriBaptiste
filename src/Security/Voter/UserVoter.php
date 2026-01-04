<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const MANAGE = 'MANAGE_USERS';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::MANAGE;
        // Logic: specific subjects can be checked here if needed, but for "Manage Users" globally or per user, we rely on the attribute.
        // If we want to check permissions on a specific User instance, we would verify $subject instanceof User.
        // For general "Access User Management", $subject might be null.
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // check if the user is an admin
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        return false;
    }
}
