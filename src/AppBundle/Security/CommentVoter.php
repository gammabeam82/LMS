<?php

namespace AppBundle\Security;

use AppBundle\Entity\Comment;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CommentVoter extends Voter
{
    /**
     * @param string $attribute
     * @param Comment $subject
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        if (false === in_array($attribute, [Actions::EDIT, Actions::DELETE])) {
            return false;
        }

        if (false === $subject instanceof Comment) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param Comment $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (false === $user instanceof User) {
            return false;
        }

        $comment = $subject;

        switch ($attribute) {
            case Actions::EDIT:
                return $this->canEdit($comment, $user);
            case Actions::DELETE:
                return $this->canDelete($user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    /**
     * @param Comment $comment
     * @param User $user
     * @return bool
     */
    private function canEdit(Comment $comment, User $user): bool
    {
        $timeDiff = date_timestamp_get(new \DateTime()) - $comment->getPostedAt()->getTimestamp();

        return ($user === $comment->getUser() && $timeDiff < 600) || $user->hasRole('ROLE_ADMIN');
    }

    /**
     * @param User $user
     * @return bool
     */
    private function canDelete(User $user): bool
    {
        return $user->hasRole('ROLE_ADMIN');
    }
}
