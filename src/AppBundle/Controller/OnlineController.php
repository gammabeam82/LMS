<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class OnlineController extends Controller
{
    /**
     * @Route("/online", name="users_count")
     *
     * @return JsonResponse
     */
    public function usersCountAction()
    {
        $redis = $this->get('snc_redis.default');

        return new JsonResponse([
            'usersCount' => count($redis->keys("user:*"))
        ]);
    }
}
