<?php

declare(strict_types=1);

namespace FromHome\Kutt\Input;

use FromHome\Kutt\Request;

final class ListShortLinkInput extends GetInput
{
    public static function create(self | array $input): self
    {
        return $input instanceof self ? $input : new self(
            $input['limit'] ?? 10,
            $input['skip'] ?? 0,
            $input['all'] ?? false
        );
    }

    public function request(): Request
    {
        return Request::create('GET', '/links', [], [
            'limit' => $this->limit,
            'skip' => $this->skip,
            'all' => $this->all,
        ]);
    }
}
