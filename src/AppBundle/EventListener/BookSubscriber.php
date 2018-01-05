<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Book;
use AppBundle\Event\BookEvent;
use AppBundle\Events;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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
            $this->producer->publish($this->prepareData($book, $this->adminMail));

            $subscribers = $book->getAuthor()->getSubscribers();

            foreach ($subscribers as $subscriber) {
                if ($subscriber->getEmail() !== $this->adminMail) {
                    $this->producer->publish($this->prepareData($book, $subscriber->getEmail()));
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

    /**
     * @param Book $book
     * @param string $email
     *
     * @return string
     */
    private function prepareData(Book $book, string $email): string
    {
        return serialize([
            'book' => $book,
            'email' => $email
        ]);
    }
}
