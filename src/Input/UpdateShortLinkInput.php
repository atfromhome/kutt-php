<?php

declare(strict_types=1);

namespace FromHome\Kutt\Input;

use FromHome\Kutt\Request;

final class UpdateShortLinkInput extends Input
{
    private string $id;

    private array $params;

    public function __construct(
        string $id,
        string $target,
        string $address,
        ?string $description = null,
        ?string $expireIn = null
    ) {
        $this->id = $id;
        $this->params = [
            'target' => $target,
            'address' => $address,
            'description' => $description,
            'expire_in' => $expireIn,
        ];
    }

    public static function create(self | array $input): self
    {
        return $input instanceof self ? $input : new self(
            $input['id'],
            $input['target'],
            $input['address'],
            $input['description'] ?? null,
            $input['expire_in'] ?? null,
        );
    }

    public function request(): Request
    {
        return Request::create('PATCH', '/links/' . $this->id, $this->params);
    }
}
