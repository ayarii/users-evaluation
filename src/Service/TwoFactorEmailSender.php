<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class TwoFactorCodeEmailSender
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendTwoFactorCodeEmail(string $recipientEmail, string $code): void
    {
       
        $email = (new Email())
            ->from('usersevaluatio@example.com')
            ->to($recipientEmail)
            ->subject('Two-Factor Authentication Code')
            ->html("email/two_factor_authentication.html.twig");

        $this->mailer->send($email);
    }
}
