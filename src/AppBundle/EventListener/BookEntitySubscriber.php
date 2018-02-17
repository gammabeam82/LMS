<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Book;
use AppBundle\Service\Cache\CacheServiceInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class BookEntitySubscriber implements EventSubscriber
{
    /**
     * @var CacheServiceInterface
     */
    private $cacheService;

    /**
     * BookEntitySubscriber constructor.
     *
     * @param CacheServiceInterface $cacheService
     */
    public function __construct(CacheServiceInterface $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->processEvent($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->processEvent($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args): void
    {
        $this->processEvent($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    private function processEvent(LifecycleEventArgs $args): void
    {
        if (false !== $args->getObject() instanceof Book) {
            $this->cacheService->clearCache();
        }
    }
}
