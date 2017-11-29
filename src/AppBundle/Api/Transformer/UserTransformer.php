<?php

namespace AppBundle\Api\Transformer;

use AppBundle\Entity\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract implements TransformerInterface
{
    /**
     * @param User $user
     * @return array
     */
    public function transform(User $user): array
    {
        return [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail()
        ];
    }
}
