<?php

namespace App\Service;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Twig\Environment;

class TwoFactorCodeGenerator
{
    private $emailSender;
    private $passwordEncoder;

    public function __construct(TwoFactorCodeEmailSender $emailSender, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->emailSender = $emailSender;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function generateAndSendCode(string $email): void
    {
        // Generate the 2FA code
        $code = $this->generateCode();


        // Send the email with the code
        $this->emailSender->sendTwoFactorCodeEmail($email, $code);
    }
    private function generateCode(): string
    {
        // Implement your code generation logic here
        // You can generate a random code or use a specific algorithm

        // Example: Generate a random 6-digit code
        $code = rand(100000, 999999);

        return (string) $code;
    }

    // ...
}
