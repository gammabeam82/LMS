<?php

namespace AppBundle\Service;

use AppBundle\Utils\DoctrineAwareTrait;
use AppBundle\Utils\EntityTrait;

abstract class AbstractService implements ServiceInterface
{
    use DoctrineAwareTrait;
    use EntityTrait;
}
