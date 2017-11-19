<?php

namespace AppBundle\EventListener;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use AppBundle\Entity\User;
use AppBundle\Service\Online\OnlineInterface;

class RequestListener
{

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var OnlineInterface
     */
    private $onlineService;

    /**
     * RequestListener constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     * @param OnlineInterface $onlineService
     */
    public function __construct(TokenStorageInterface $tokenStorage, OnlineInterface $onlineService)
    {
        $this->tokenStorage = $tokenStorage;
        $this->onlineService = $onlineService;
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

        $this->onlineService->storeUser($user);
    }
}
