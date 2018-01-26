<?php

namespace AppBundle\Api\Controller;

use AppBundle\Api\Provider\ApiDataProvider;
use AppBundle\Api\Transformer\UserTransformer;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

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
