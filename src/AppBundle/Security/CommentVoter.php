<?php

namespace AppBundle\Security;

use AppBundle\Entity\Comment;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CommentVoter extends Voter
{
	const EDIT = 'edit';
	const DELETE = 'delete';

	/**
	 * @param string $attribute
	 * @param Comment $subject
	 * @return bool
	 */
	protected function supports($attribute, $subject)
	{
		if (false === in_array($attribute, [self::EDIT, self::DELETE])) {
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
			case self::DELETE:
				return $this->canDelete($user);
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
		$timeDiff = date_timestamp_get(new \DateTime()) - date_timestamp_get($comment->getPostedAt());

		return ($user === $comment->getUser() && $timeDiff < 600) || $user->hasRole('ROLE_ADMIN');
	}

	/**
	 * @param User $user
	 * @return bool
	 */
	private function canDelete(User $user)
	{
		return $user->hasRole('ROLE_ADMIN');
	}
}