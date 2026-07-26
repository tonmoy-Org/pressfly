<?php

namespace App\Mailer\Transports;

use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;

class PHPMailerSmtpTransport extends AbstractTransport
{
    /**
     * @throws \PHPMailer\PHPMailer\Exception|\Exception
     */
    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Host = str_replace('ssl://', '', get_option('email_smtp_host'));
        $mail->SMTPSecure = str_replace('none', '', get_option('email_smtp_security'));
        $mail->Port = get_option('email_smtp_port');
        $mail->SMTPAuth = true;
        $mail->Username = get_option('email_smtp_username');
        $mail->Password = get_option('email_smtp_password');
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];

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

        /** @var \Symfony\Component\Mime\Part\DataPart[] $attachments */
//        $attachments = $email->getAttachments();
//        $email->attachFromPath();
//        foreach ($attachments as $attachment) {
//            $mail->addAttachment();
//        }

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
        return 'phpmailer-smtp';
    }
}
