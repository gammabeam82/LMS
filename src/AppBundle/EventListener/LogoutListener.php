<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

class LogoutListener implements LogoutHandlerInterface
{
    /**
     * @var \Predis\Client
     */
    private $redis;

    /**
     * LogoutListener constructor.
     * @param \Predis\Client $redis
     */
    public function __construct(\Predis\Client $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param TokenInterface $token
     */
    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        $user = $token->getUser();

        $this->redis->del([
            sprintf("user:%s", $user->getId())
        ]);
    }
}
