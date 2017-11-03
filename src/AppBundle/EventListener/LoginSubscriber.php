<?php

namespace AppBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class LoginSubscriber implements EventSubscriberInterface
{
    const PERIOD = 60;
    const MAX_ATTEMPTS = 3;

    /**
     * @var \Predis\Client
     */
    private $redis;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * LoginSubscriber constructor.
     * @param \Predis\Client $redis
     * @param TokenStorageInterface $tokenStorage
     * @param RequestStack $request
     */
    public function __construct(\Predis\Client $redis, TokenStorageInterface $tokenStorage, RequestStack $request)
    {
        $this->redis = $redis;
        $this->tokenStorage = $tokenStorage;
        $this->request = $request;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            AuthenticationEvents::AUTHENTICATION_FAILURE => 'onAuthenticationFailure',
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
        ];
    }

    /**
     * @param AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailure(AuthenticationFailureEvent $event)
    {
        $this->processLoginAttempt($event);
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $attempts = $this->processLoginAttempt($event);

        if ($attempts >= self::MAX_ATTEMPTS) {

            $this->tokenStorage->setToken(null);
            $this->request->getMasterRequest()->getSession()->invalidate();

            throw new BadCredentialsException();
        }
    }

    /**
     * @param AuthenticationFailureEvent|InteractiveLoginEvent $event
     *
     * @return int
     */
    private function processLoginAttempt($event)
    {
        $timestamp = time();
        $key = $event->getAuthenticationToken()->getUsername();

        if (false !== $event instanceof AuthenticationFailureEvent) {
            $member = sprintf("%s:%s", $key, $timestamp);
            $this->redis->zadd($key, [$member => $timestamp]);
        }

        $this->redis->zremrangebyscore($key, 0, $timestamp - self::PERIOD);

        return $this->redis->zcard($key);
    }
}
