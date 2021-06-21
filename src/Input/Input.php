<?php

declare(strict_types=1);

namespace FromHome\Kutt\Input;

use FromHome\Kutt\Request;

abstract class Input
{
    abstract public function request(): Request;
}
