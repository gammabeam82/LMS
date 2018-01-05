<?php

namespace AppBundle\Consumer;

use AppBundle\Entity\Book;
use AppBundle\Service\Mail\MailerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;


class MailSenderConsumer implements ConsumerInterface
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * MailSenderConsumer constructor
     * .
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @var AMQPMessage $msg
     * @return void
     */
    public function execute(AMQPMessage $msg): void
    {
        $data = unserialize($msg->getBody());

        /** @var Book $book  */
        $book = $data['book'];
        $email = $data['email'];

        $this->mailer->sendNotification($book, $email);

        echo "Notification sent to ${email}";
        echo PHP_EOL;
    }
}
