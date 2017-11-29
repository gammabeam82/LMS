<?php

namespace AppBundle\Api\Controller;

use AppBundle\Api\Provider\ApiDataProvider;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Api\Transformer\UserTransformer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class UsersApiController extends Controller
{

    /**
     * @Route("/api/users/{id}", name="api_users_get")
     * @ParamConverter("user")
     * @Method({"GET"})
     *
     * @param User $user
     * @return JsonResponse
     */
    public function getUserAction(User $user): JsonResponse
    {
        $provider = new ApiDataProvider([
            'items' => [$user],
            'transformer' => new UserTransformer()
        ]);

        return new JsonResponse($provider->getData());
    }
}
