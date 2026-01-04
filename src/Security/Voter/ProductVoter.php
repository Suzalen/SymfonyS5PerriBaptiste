<?php

namespace App\Security\Voter;

use App\Entity\Product;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductVoter extends Voter
{
    public const EDIT = 'PRODUCT_EDIT';
    public const DELETE = 'PRODUCT_DELETE';
    public const VIEW = 'PRODUCT_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // View can be global (list) or subject-specific (details)
        if ($attribute === self::VIEW) {
            return true;
        }

        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Product;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // All authenticated users can view
        if ($attribute === self::VIEW) {
            return $token->getUser() instanceof UserInterface;
        }

        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Only Admin can edit/delete
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        return false;
    }
}
