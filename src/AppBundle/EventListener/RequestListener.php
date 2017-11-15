<?php

namespace AppBundle\EventListener;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use AppBundle\Entity\User;
use AppBundle\Utils\RedisAwareTrait;

class RequestListener
{
    use RedisAwareTrait;

    private const EXPIRE = 50;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * RequestListener constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event): void
    {
        $token = $this->tokenStorage->getToken();

        if (false === $event->isMasterRequest() || null === $token) {
            return;
        }

        $user = $token->getUser();

        if (false === $user instanceof User) {
            return;
        }

        $key = sprintf("user:%s", $user->getId());

        if (1 !== $this->redis->exists($key)) {
            $this->redis->hmset($key, [
                'id' => $user->getId(),
                'name' => $user->getUsername()
            ]);
        }

        $this->redis->expire($key, self::EXPIRE);
    }
}
