<?php

namespace Mbl\Api;

interface Webhook
{
    public function toArray(): array;

    public function trigger(array $params);
}
