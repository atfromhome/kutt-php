<?php

declare(strict_types=1);

namespace FromHome\Kutt;

final class Credentials
{
    private string $key;

    private string $baseUrl;

    public function __construct(string $baseUrl, string $key)
    {
        $this->baseUrl = $baseUrl;
        $this->key = $key;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}
