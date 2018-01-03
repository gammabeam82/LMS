<?php

namespace AppBundle\EventListener;

use AppBundle\Events;
use AppBundle\Service\Mail\MailerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use AppBundle\Event\BookEvent;

class BookSubscriber implements EventSubscriberInterface
{
    private const ON_BOOK_CREATED = 'onBookCreated';
    private const ON_BOOK_DELETED = 'onBookDeleted';

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * BookSubscriber constructor.
     *
     * @param MailerInterface $mailer
     * @param LoggerInterface $logger
     */
    public function __construct(MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::BOOK_CREATED => self::ON_BOOK_CREATED,
            Events::BOOK_DELETED => self::ON_BOOK_DELETED,
        ];
    }

    /**
     * @param BookEvent $event
     */
    public function onBookCreated(BookEvent $event): void
    {
        $book = $event->getBook();

        $this->mailer->sendNotification($book);
        $this->logger->info(sprintf("New: [Book: %s User: %s]", $book->getName(), $book->getAddedBy()->getUsername()));
    }

    /**
     * @param BookEvent $event
     */
    public function onBookDeleted(BookEvent $event): void
    {
        $book = $event->getBook();

        $this->logger->info(sprintf("Deleted: [Book: %s User: %s]", $book->getName(), $book->getAddedBy()->getUsername()));
    }
}
