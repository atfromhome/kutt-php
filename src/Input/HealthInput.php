<?php

declare(strict_types=1);

namespace FromHome\Kutt\Input;

use FromHome\Kutt\Request;

final class HealthInput extends Input
{
    public function request(): Request
    {
        return Request::create('GET', '/health');
    }
}
