<?php

declare(strict_types=1);

namespace FromHome\Kutt;

final class Request
{
    private string $method;

    private string $endPoint;

    private array $params;

    private array $query;

    public function __construct(string $method, string $endPoint, array $params = [], array $query = [])
    {
        $this->method = $method;
        $this->endPoint = $endPoint;
        $this->params = $params;
        $this->query = $query;
    }

    public static function create(string $method, string $endPoint, array $params = [], array $query = []): self
    {
        return new self($method, $endPoint, $params);
    }

    public function getEndPoint(): string
    {
        return $this->endPoint;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getQuery(): array
    {
        return $this->query;
    }
}
