<?php

namespace Mbl\AutoResponder;

interface Block
{
    public function tree($options = []): array;
}