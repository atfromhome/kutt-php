<?php

declare(strict_types=1);

namespace FromHome\Kutt\Input;

use FromHome\Kutt\Request;

final class ShowShortLinkInput extends Input
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function create(self | array $input): self
    {
        return $input instanceof self ? $input : new self($input['id']);
    }

    public function request(): Request
    {
        return Request::create('GET', '/links/' . $this->id . '/stats');
    }
}
