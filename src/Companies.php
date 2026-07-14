<?php

declare(strict_types=1);

namespace Emitfy;

final class Companies extends Resource
{
    public function list(): mixed
    {
        return $this->http->request('GET', '/companies');
    }

    /** @param array<string, mixed> $payload */
    public function create(array $payload): mixed
    {
        return $this->http->request('POST', '/companies', $payload);
    }
}
