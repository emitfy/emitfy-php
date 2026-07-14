<?php

declare(strict_types=1);

namespace Emitfy;

abstract class Resource
{
    public function __construct(protected readonly HttpClient $http)
    {
    }
}
