<?php

namespace AppBundle\Service\Online;

use AppBundle\Entity\User;

interface OnlineInterface
{
    public function getUsersOnline(): array;

    public function getUsersOnlineCount(): int;

    public function storeUser(User $user): void;
}
