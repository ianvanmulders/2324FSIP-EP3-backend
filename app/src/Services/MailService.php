<?php

namespace Services;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

class MailService
{
    public static function send( string $from, string $to, string $subject, string $text, string $message, string $fname, string $lname) {


        $transport = Transport::fromDsn('smtp://' . MAIL_USER . ':' . MAIL_PASS . '@' . MAIL_HOST . ':' . MAIL_PORT . '?' . MAIL_OPTIONS );
        $mailer = new Mailer($transport);

        $email = (new Email())
            ->from($from)
            ->to($to)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->text($text)
            ->html('<p> Beste </br> </br>'.$message.'</br> </br>Met vriendelijke groeten </br>'. $fname .' '. $lname.'</p>');
        ;
        $mailer->send($email);
    }
}