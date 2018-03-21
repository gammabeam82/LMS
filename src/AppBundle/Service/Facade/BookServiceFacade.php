<?php

namespace AppBundle\Service\Facade;

use AppBundle\Service\Cache\CacheServiceInterface;
use AppBundle\Service\Interfaces\ArchiveServiceInterface;
use AppBundle\Service\Interfaces\BookServiceInterface;
use AppBundle\Service\Interfaces\SessionServiceInterface;
use AppBundle\Service\ServiceInterface;

class BookServiceFacade
{
    /**
     * @var ArchiveServiceInterface
     */
    private $archiveService;

    /**
     * @var BookServiceInterface
     */
    private $bookService;

    /**
     * @var CacheServiceInterface
     */
    private $cacheService;

    /**
     * @var SessionServiceInterface
     */
    private $sessionService;

    /**
     * BookServiceFacade constructor.
     *
     * @param ArchiveServiceInterface $archives
     * @param BookServiceInterface $books
     * @param CacheServiceInterface $cache
     * @param SessionServiceInterface $sessions
     */
    public function __construct(ArchiveServiceInterface $archives, BookServiceInterface $books, CacheServiceInterface $cache, SessionServiceInterface $sessions)
    {
        $this->archiveService = $archives;
        $this->bookService = $books;
        $this->cacheService = $cache;
        $this->sessionService = $sessions;
    }

    /**
     * @param string $name
     *
     * @return ServiceInterface
     */
    public function __get(string $name): ServiceInterface
    {
        if (false === property_exists($this, $name)) {
            throw new \InvalidArgumentException();
        }

        return $this->$name;
    }

    /**
     * @return ArchiveServiceInterface
     */
    public function archiveService(): ArchiveServiceInterface
    {
        return $this->archiveService;
    }

    /**
     * @return BookServiceInterface
     */
    public function bookService(): BookServiceInterface
    {
        return $this->bookService;
    }

    /**
     * @return CacheServiceInterface
     */
    public function cacheService(): CacheServiceInterface
    {
        return $this->cacheService;
    }

    /**
     * @return SessionServiceInterface
     */
    public function sessionService(): SessionServiceInterface
    {
        return $this->sessionService;
    }
}
