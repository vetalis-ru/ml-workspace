<?php

namespace Mbl\Api;

class CachedWebhook extends WebhookDc implements Webhook
{
    private Webhook $origin;
    private array $cache;

    public function __construct(Webhook $webhook, array $cachedData)
    {
        parent::__construct($webhook);
        $this->origin = $webhook;
        $this->cache = $cachedData;
    }

    public function toArray(): array
    {
        if (empty($this->cache)) {
            $this->cache = $this->origin->toArray();
        }

        return $this->cache;
    }
}