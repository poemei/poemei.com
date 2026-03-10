<?php

require_once __DIR__ . '/phpmailer/Exception.php';
require_once __DIR__ . '/phpmailer/PHPMailer.php';
require_once __DIR__ . '/phpmailer/SMTP.php';

class mailer
{
    /**
     * Create and configure a PHPMailer instance with hardcoded credentials.
     */
    public function create(): \PHPMailer\PHPMailer\PHPMailer
    {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        // Hardcoded Configuration
        $mail->isSMTP();
        $mail->Host       = 'mail.poemei.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'contact@poemei.com';
        $mail->Password   = 'LsXJNW9S#';
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';
        $mail->isHTML(true);

        $mail->setFrom('poe@poemei.com', 'Poe Mei');

        return $mail;
    }
}
