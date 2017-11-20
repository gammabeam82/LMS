<?php

namespace AppBundle\Security\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use AppBundle\Service\Online\OnlineInterface;

class LogoutHandler implements LogoutHandlerInterface
{

    /**
     * @var OnlineInterface
     */
    private $onlineService;

    /**
     * LogoutHandler constructor.
     *
     * @param OnlineInterface $onlineService
     */
    public function __construct(OnlineInterface $onlineService)
    {
        $this->onlineService = $onlineService;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param TokenInterface $token
     */
    public function logout(Request $request, Response $response, TokenInterface $token): void
    {
        $this->onlineService->removeUser($token->getUser());
    }
}
