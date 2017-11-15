<?php

namespace AppBundle\Security\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use AppBundle\Utils\RedisAwareTrait;

class LogoutHandler implements LogoutHandlerInterface
{
    use RedisAwareTrait;

    /**
     * LogoutListener constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param TokenInterface $token
     */
    public function logout(Request $request, Response $response, TokenInterface $token): void
    {
        $user = $token->getUser();

        $this->redis->del([
            sprintf("user:%s", $user->getId())
        ]);
    }
}
