<?php

namespace AppBundle\EventListener;

use AppBundle\Events;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use AppBundle\Event\BookEvent;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

class BookSubscriber implements EventSubscriberInterface
{
    private const ON_BOOK_CREATED = 'onBookCreated';
    private const ON_BOOK_DELETED = 'onBookDeleted';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ProducerInterface
     */
    private $producer;

    /**
     * @var string
     */
    private $environment;

    /**
     * @var string
     */
    private $adminMail;

    public function __construct(LoggerInterface $logger, ProducerInterface $producer, string $environment, string $adminMail)
    {
        $this->logger = $logger;
        $this->producer = $producer;
        $this->environment = $environment;
        $this->adminMail = $adminMail;
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

        if ($this->environment !== 'test') {

            $this->logger->info(sprintf("New: [Book: %s User: %s]", $book->getName(), $book->getAddedBy()->getUsername()));

            $this->producer->publish(serialize([
                'book' => $book,
                'email' => $this->adminMail
            ]));

            $subscribers = $book->getAuthor()->getSubscribers();

            if(count($subscribers)) {
                foreach($subscribers as $subscriber) {
                    $this->producer->publish(serialize([
                        'book' => $book,
                        'email' => $subscriber->getEmail()
                    ]));
                }
            }
        }
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
