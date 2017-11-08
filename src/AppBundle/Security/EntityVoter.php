<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use AppBundle\Entity\EntityInterface;

abstract class EntityVoter extends Voter
{
    protected const VIEW = 'view';
    protected const EDIT = 'edit';
    protected const DELETE = 'delete';
    protected const CREATE = 'create';

    /**
     * @param string $attribute
     * @param EntityInterface $subject
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        if (false === in_array($attribute, [self::EDIT, self::DELETE, self::CREATE, self::VIEW])) {
            return false;
        }

        if (false === $subject instanceof EntityInterface) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param EntityInterface $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (false === $user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($user);
            case self::DELETE:
                return $this->canDelete($user);
            case self::CREATE:
                return $this->canCreate($user);
            case self::VIEW:
                return $this->canView($user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    /**
     * @param User $user
     * @return bool
     */
    private function canEdit(User $user): bool
    {
        return $user->hasRole('ROLE_ADMIN');
    }

    /**
     * @param User $user
     * @return bool
     */
    private function canDelete(User $user): bool
    {
        return $user->hasRole('ROLE_ADMIN');
    }

    /**
     * @param User $user
     * @return bool
     */
    private function canCreate(User $user): bool
    {
        return $user->hasRole('ROLE_ADMIN');
    }

    /**
     * @param User $user
     * @return bool
     */
    private function canView(User $user): bool
    {
        return $user->hasRole('ROLE_USER');
    }
}
