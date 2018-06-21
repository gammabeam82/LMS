<?php

namespace AppBundle\Service\Mail;

use AppBundle\Entity\Book;
use Swift_Mailer as SwiftMailer;
use Swift_Message as SwiftMessage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;

class Mailer implements MailerInterface
{
    /**
     * @var SwiftMailer
     */
    private $mailer;

    /**
     * @var EngineInterface
     */
    private $twig;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * Mail constructor.
     *
     * @param SwiftMailer $mailer
     * @param EngineInterface $twig
     * @param RouterInterface $router
     */
    public function __construct(SwiftMailer $mailer, EngineInterface $twig, RouterInterface $router)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->router = $router;
    }

    /**
     * @param Book $book
     */
    public function sendNotification(Book $book, string $email): void
    {
        $message = new SwiftMessage();
        $message
            ->setFrom('noreply@lms')
            ->setSubject('New book')
            ->setTo($email)
            ->setBody($this->twig->render('email/notification.html.twig', [
                'book' => $book,
                'url' => $this->router->generate('books_show', ['id' => $book->getId()], UrlGeneratorInterface::ABSOLUTE_URL)
            ]))
            ->setContentType('text/html');

        $this->mailer->send($message);
    }
}
