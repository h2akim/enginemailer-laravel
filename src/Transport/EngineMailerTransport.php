<?php

namespace HakimRazalan\EngineMailerLaravel\Transport;

use HakimRazalan\EngineMailer\Client as EngineMailer;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\MessageConverter;
use Symfony\Component\Mime\Part\DataPart;

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
        $sender = collect($email->getFrom())->first();
        $mailer = $mailer
            ->setSenderEmail($sender->getAddress())
            ->setSenderName($sender->getName())
            ->setToEmail(collect($email->getTo())->first()->getAddress())
            ->setSubject($email->getSubject())
            ->setSubmittedContent($email->getHtmlBody())
            ->setCCEmails(collect($email->getCc())->map(function (Address $address): string {
                return $address->getAddress();
            })->toArray())
            ->setBCCEmails(collect($email->getBcc())->map(function (Address $address): string {
                return $address->getAddress();
            })->toArray());

        // Handle attachments
        if ($email->getAttachments() !== []) {
            $attachments = collect($email->getAttachments())->map(function (DataPart $data): array {
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