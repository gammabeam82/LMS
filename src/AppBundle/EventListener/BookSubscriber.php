<?php

namespace AppBundle\EventListener;

use AppBundle\BookEvents;
use AppBundle\Service\Mail\MailerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use AppBundle\Event\BookEvent;

class BookSubscriber implements EventSubscriberInterface
{
    private const ON_BOOK_CREATED = 'onBookCreated';

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
            BookEvents::BOOK_CREATED => self::ON_BOOK_CREATED,
        ];
    }

    /**
     * @param BookEvent $event
     */
    public function onBookCreated(BookEvent $event): void
    {
        $book = $event->getBook();

        $this->mailer->sendNotification($book);
        $this->logger->info(sprintf("Book: %s User: %s", $book->getName(), $book->getAddedBy()->getUsername()));
    }
}
