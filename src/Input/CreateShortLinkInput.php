<?php

declare(strict_types=1);

namespace FromHome\Kutt\Input;

use FromHome\Kutt\Request;

final class CreateShortLinkInput extends Input
{
    private array $params;

    public function __construct(
        string $target,
        ?string $customUrl = null,
        ?string $description = null,
        ?string $expireIn = null,
        ?string $password = null,
        bool $reuse = false,
        ?string $domain = null
    ) {
        $this->params = [
            'target' => $target,
            'description' => $description,
            'expire_in' => $expireIn,
            'password' => $password,
            'customurl' => $customUrl,
            'reuse' => $reuse,
            'domain' => $domain,
        ];
    }

    public static function create(self | array $input): self
    {
        return $input instanceof self ? $input : new self(
            $input['target'],
            $input['description'] ?? null,
            $input['expire_in'] ?? null,
            $input['password'] ?? null,
            $input['customUrl'] ?? null,
            $input['reuse'] ?? false,
            $input['domain'] ?? null,
        );
    }

    public function request(): Request
    {
        return Request::create('POST', '/links', $this->params);
    }
}
