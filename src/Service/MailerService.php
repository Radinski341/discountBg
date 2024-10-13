<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail($to, $subject, $body)
    {
        $email = (new Email())
            ->from('radinskikire@gmail.com')
            ->to($to)
            ->subject($subject)
            ->text($body);

        $this->mailer->send($email);
    }
}