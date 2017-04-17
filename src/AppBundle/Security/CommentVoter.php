<?php

namespace AppBundle\Security;

namespace AppBundle\Security;

use AppBundle\Entity\Comment;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CommentVoter extends Voter
{
	const EDIT = 'edit';

	/**
	 * @param string $attribute
	 * @param Comment $subject
	 * @return bool
	 */
	protected function supports($attribute, $subject)
	{
		if (false === in_array($attribute, [self::EDIT])) {
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
	protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
	{
		$user = $token->getUser();

		if (false === $user instanceof User) {
			return false;
		}

		$comment = $subject;

		switch ($attribute) {
			case self::EDIT:
				return $this->canEdit($comment, $user);
		}

		throw new \LogicException('This code should not be reached!');
	}

	/**
	 * @param Comment $comment
	 * @param User $user
	 * @return bool
	 */
	private function canEdit(Comment $comment, User $user)
	{
		return $user === $comment->getUser() || $user->hasRole('ROLE_ADMIN');
	}
}