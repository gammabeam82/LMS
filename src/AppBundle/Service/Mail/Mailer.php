<?php

namespace AppBundle\Service\Mail;

use AppBundle\Entity\Book;
use Swift_Mailer as SwiftMailer;
use Swift_Message as SwiftMessage;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
     * @var string
     */
    private $email;

    /**
     * Mail constructor.
     *
     * @param SwiftMailer $mailer
     * @param EngineInterface $twig
     * @param RouterInterface $router
     * @param string $email
     */
    public function __construct(SwiftMailer $mailer, EngineInterface $twig, RouterInterface $router, string $email)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->router = $router;
        $this->email = $email;
    }

    /**
     * @param Book $book
     */
    public function sendNotification(Book $book): void
    {
        $message = new SwiftMessage();
        $message
            ->setFrom('noreply@lms')
            ->setTo($this->email)
            ->setBody($this->twig->render('email/notification.html.twig', [
                'book' => $book,
                'url' => $this->router->generate('books_view', ['id' => $book->getId()], UrlGeneratorInterface::ABSOLUTE_URL)
            ]))
            ->setContentType('text/html');

        $this->mailer->send($message);
    }
}
