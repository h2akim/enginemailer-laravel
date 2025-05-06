<?php

namespace HakimRazalan\EngineMailerDriver\Transport;

use HakimRazalan\EngineMailer\Client as EngineMailer;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\MessageConverter;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\RawMessage;

class EngineMailerTransport extends AbstractTransport
{
    public function __construct(
        protected EngineMailer $engineMailer
    ) {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $mailer = $this->engineMailer->sendEmail();
        /** @var \HakimRazalan\EngineMailer\Two\Submission\SendEmail $mailer */
        $mailer = $mailer
            ->setSenderEmail($email->getFrom())
            ->setSenderName($email->getSender())
            ->setToEmail(collect($email->getTo())->first()->getAddress())
            ->setSubject($email->getSubject())
            ->setSubmittedContent($email->getHtmlBody())
            ->setCCEmails(collect($email->getCc())->map(function (Address $address) {
                return $address->getAddress();
            })->toArray())
            ->setBCCEmails(collect($email->getBcc())->map(function (Address $address) {
                return $address->getAddress();
            })->toArray());

        // Handle attachments
        if (! empty($email->getAttachments())) {
            $attachments = collect($email->getAttachments())->map(function (DataPart $data) {
                return [
                    'Filename' => $data->getFilename(),
                    'Content' => $data->bodyToString()
                ];
            })->toArray();

            $mailer = $mailer->setAttachments($attachments);
        }
            
        $mailer->handle();
    }

    public function __toString(): string {
        return 'enginemailer';
    }
}