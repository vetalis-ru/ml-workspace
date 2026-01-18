<?php

namespace Mbl\Api;

abstract class WebhookDc implements Webhook
{

    private Webhook $webhook;

    public function __construct(Webhook $webhook)
    {
        $this->webhook = $webhook;
    }

    public function toArray(): array
    {
        return $this->webhook->toArray();
    }

    public function trigger(array $params)
    {
        $this->webhook->trigger($params);
    }
}