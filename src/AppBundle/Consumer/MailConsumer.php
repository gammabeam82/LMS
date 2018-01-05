<?php

namespace AppBundle\Consumer;

use AppBundle\Entity\Book;
use AppBundle\Service\Mail\MailerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;


class MailConsumer implements ConsumerInterface
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

        echo "Ready to accept messages...\n\n";
    }

    /**
     * @var AMQPMessage $msg
     * @return void
     */
    public function execute(AMQPMessage $msg): void
    {
        $data = unserialize($msg->getBody());

        /** @var Book $book */
        $book = $data['book'];
        $email = $data['email'];

        $this->mailer->sendNotification($book, $email);

        printf("%s - notification sent to %s \n", date("d.m.Y H:i:s"), $email);
    }
}
