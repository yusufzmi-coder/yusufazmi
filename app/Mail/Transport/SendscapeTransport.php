<?php

declare(strict_types=1);

namespace App\Mail\Transport;

use App\Integrations\IntegrationManager;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\MessageConverter;

/**
 * Sendscape ships no Laravel driver, so this is a thin Symfony transport over its
 * HTTP API. Credentials are read through the IntegrationManager, which means a key
 * changed in the admin widget takes effect on the very next send.
 */
final class SendscapeTransport extends AbstractTransport
{
    private const ENDPOINT = 'https://api.sendscape.ai/v1/emails';

    public function __construct(private readonly IntegrationManager $manager)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        $config = $this->manager->config('sendscape');

        if (blank($config['api_key'] ?? null)) {
            throw new TransportException('Kunci API Sendscape belum ditetapkan.');
        }

        $response = Http::withToken($config['api_key'])
            ->timeout(20)
            ->acceptJson()
            ->post(self::ENDPOINT, array_filter([
                'from' => $this->addresses($email->getFrom())[0] ?? $config['from_address'],
                'to' => $this->addresses($email->getTo()),
                'subject' => $email->getSubject(),
                'html' => $email->getHtmlBody(),
                'text' => $email->getTextBody(),
                'reply_to' => $this->addresses($email->getReplyTo())[0] ?? null,
            ]));

        // The API acknowledges a queued send with 202 Accepted.
        if ($response->status() !== 202 && ! $response->successful()) {
            throw new TransportException(
                "Sendscape menolak emel ({$response->status()}): ".$response->body(),
            );
        }
    }

    /**
     * @param  array<int, Address>  $addresses
     * @return list<string>
     */
    private function addresses(array $addresses): array
    {
        return array_values(array_map(
            static fn ($address): string => $address->getAddress(),
            $addresses,
        ));
    }

    public function __toString(): string
    {
        return 'sendscape';
    }
}
