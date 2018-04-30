<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OnlineController extends Controller
{
    /**
     * @Route("/online/users", name="users_online")
     *
     * @return JsonResponse
     */
    public function usersOnlineAction()
    {
        $users = $this->get('app.online')->getUsersOnline();

        return new JsonResponse($users);
    }

    /**
     * @Route("/online/{type}", name="users_count")
     *
     * @param string $type
     *
     * @return JsonResponse|Response
     */
    public function usersCountAction($type)
    {
        $usersCount = $this->get('app.online')->getUsersOnlineCount();

        if ($type === 'json') {
            $response = new JsonResponse([
                'usersCount' => $usersCount
            ]);
        } else {
            $response = new Response($usersCount);
        }

        return $response;
    }
}
