<?php

declare(strict_types=1);

namespace FromHome\Kutt\Input;

abstract class GetInput extends Input
{
    protected int $limit;

    protected int $skip;

    protected bool $all;

    public function __construct(int $limit = 10, int $skip = 0, bool $all = false)
    {
        $this->limit = $limit;
        $this->skip = $skip;
        $this->all = $all;
    }
}
