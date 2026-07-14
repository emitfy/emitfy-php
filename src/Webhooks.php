<?php

declare(strict_types=1);

namespace Emitfy;

final class Webhooks extends Resource
{
    public function list(): mixed
    {
        return $this->http->request('GET', '/webhooks');
    }

    /** @param array<string, mixed> $payload */
    public function create(array $payload): mixed
    {
        return $this->http->request('POST', '/webhooks', $payload);
    }

    /** @param array<string, mixed> $payload */
    public function update(string $id, array $payload): mixed
    {
        return $this->http->request('PUT', '/webhooks/' . rawurlencode($id), $payload);
    }

    public function setActive(string $id, bool $active): mixed
    {
        return $this->http->request('PATCH', '/webhooks/' . rawurlencode($id) . '/active', [
            'active' => $active,
        ]);
    }

    public function delete(string $id): mixed
    {
        return $this->http->request('DELETE', '/webhooks/' . rawurlencode($id));
    }
}
