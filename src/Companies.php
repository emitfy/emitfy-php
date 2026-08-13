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

    public function get(string $companyId): mixed
    {
        return $this->http->request('GET', '/companies/' . rawurlencode($companyId));
    }

    /** @param array<string, mixed> $payload */
    public function update(string $companyId, array $payload): mixed
    {
        return $this->http->request('PUT', '/companies/' . rawurlencode($companyId), $payload);
    }

    public function delete(string $companyId): mixed
    {
        return $this->http->request('DELETE', '/companies/' . rawurlencode($companyId));
    }
}
