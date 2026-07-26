<?php

namespace App\Mailer\Transports;

use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;

class PHPMailerSendmailTransport extends AbstractTransport
{
    /**
     * @throws \PHPMailer\PHPMailer\Exception|\Exception
     */
    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $mail = new PHPMailer(true);

        $mail->isSendmail();

        /** @var \Symfony\Component\Mime\Address $from */
        $from = collect($email->getFrom())->first();
        $mail->setFrom($from->getAddress(), $from->getName());

        /** @var \Symfony\Component\Mime\Address[] $toCollection */
        $toCollection = collect($email->getTo());
        foreach ($toCollection as $to) {
            $mail->addAddress($to->getAddress(), $to->getName());
        }

        /** @var \Symfony\Component\Mime\Address[] $replyToCollection */
        $replyToCollection = collect($email->getReplyTo());
        foreach ($replyToCollection as $replyTo) {
            $mail->addReplyTo($replyTo->getAddress(), $replyTo->getName());
        }

        /** @var \Symfony\Component\Mime\Address[] $ccCollection */
        $ccCollection = collect($email->getCc());
        foreach ($ccCollection as $cc) {
            $mail->addCC($cc->getAddress(), $cc->getName());
        }

        /** @var \Symfony\Component\Mime\Address[] $bccCollection */
        $bccCollection = collect($email->getBcc());
        foreach ($bccCollection as $bcc) {
            $mail->addBCC($bcc->getAddress(), $bcc->getName());
        }

        $mail->Subject = $email->getSubject();

        $mail->msgHTML($email->getHtmlBody());
        $mail->AltBody = $email->getTextBody();

        $mail->send();
    }

    /**
     * Get the string representation of the transport.
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'phpmailer-sendmail';
    }
}
