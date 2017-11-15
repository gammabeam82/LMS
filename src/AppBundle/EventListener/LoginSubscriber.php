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
use Psr\Log\LoggerInterface;
use AppBundle\Utils\RedisAwareTrait;

class LoginSubscriber implements EventSubscriberInterface
{
    use RedisAwareTrait;

    private const PERIOD = 60;
    private const MAX_ATTEMPTS = 3;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * LoginSubscriber constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     * @param RequestStack $request
     * @param LoggerInterface $logger
     */
    public function __construct(TokenStorageInterface $tokenStorage, RequestStack $request, LoggerInterface $logger)
    {
        $this->tokenStorage = $tokenStorage;
        $this->request = $request;
        $this->logger = $logger;
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
    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        $attempts = $this->processLoginAttempt($event);

        if ($attempts >= self::MAX_ATTEMPTS) {
            $message = sprintf("user: %s, login attempts: %s", $event->getAuthenticationToken()->getUsername(), $attempts);
            $this->logger->warning($message);
        }
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
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
    private function processLoginAttempt($event): int
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
