<?php

namespace AppBundle\Service\Online;

use AppBundle\Entity\User;
use AppBundle\Utils\RedisAwareTrait;

class Online implements OnlineInterface
{
    use RedisAwareTrait;

    private const KEY = "user";
    private const EXPIRE = 50;

    /**
     * @return array
     */
    public function getUsersOnline(): array
    {
        $users = array_map(function ($key) {
            return $this->redis->hgetall($key);
        }, $this->redis->keys(sprintf("%s:*", self::KEY)));

        return $users;
    }

    /**
     * @return int
     */
    public function getUsersOnlineCount(): int
    {
        return count($this->redis->keys(sprintf("%s:*", self::KEY)));
    }

    /**
     * @param User $user
     */
    public function storeUser(User $user): void
    {
        $key = sprintf("%s:%s", self::KEY, $user->getId());

        if (1 !== $this->redis->exists($key)) {
            $this->redis->hmset($key, [
                'id' => $user->getId(),
                'name' => $user->getUsername()
            ]);
        }

        $this->redis->expire($key, self::EXPIRE);
    }

    /**
     * @param User $user
     */
    public function removeUser(User $user): void
    {
        $key = sprintf("%s:%s", self::KEY, $user->getId());

        $this->redis->del([$key]);
    }
}
