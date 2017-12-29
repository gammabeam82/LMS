<?php

namespace AppBundle\Service\Mail;

use AppBundle\Entity\Book;

interface MailerInterface
{
    public function sendNotification(Book $book): void;
}
