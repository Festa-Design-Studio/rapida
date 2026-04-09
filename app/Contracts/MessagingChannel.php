<?php

namespace App\Contracts;

interface MessagingChannel
{
    /**
     * Process an incoming message from this channel.
     *
     * @param  array<string, mixed>  $payload  Raw webhook payload
     * @return array{message: string}  Response to send back
     */
    public function handle(array $payload): array;

    /**
     * Send an outbound message to a recipient on this channel.
     */
    public function send(string $recipient, string $message): void;

    /**
     * The channel identifier (e.g. 'whatsapp', 'telegram', 'sms').
     */
    public function channelName(): string;

    /**
     * Whether this channel is enabled for the given crisis.
     */
    public function isEnabledForCrisis(string $crisisSlug): bool;
}
