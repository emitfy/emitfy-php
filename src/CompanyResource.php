<?php

declare(strict_types=1);

namespace Emitfy;

final class CompanyResource extends Resource
{
    public function __construct(
        HttpClient $http,
        private readonly string $basePath,
    ) {
        parent::__construct($http);
    }

    /** @param array<string, scalar> $query */
    public function list(array $query = []): mixed
    {
        $path = $this->basePath;

        if ($query !== []) {
            $path .= '?' . http_build_query($query);
        }

        return $this->http->request('GET', $path);
    }

    /** @param array<string, mixed> $payload */
    public function create(array $payload, ?string $idempotencyKey = null): mixed
    {
        $headers = $idempotencyKey ? ['Idempotency-Key' => $idempotencyKey] : [];

        return $this->http->request('POST', $this->basePath, $payload, $headers);
    }

    public function get(string $id): mixed
    {
        return $this->http->request('GET', $this->basePath . '/' . rawurlencode($id));
    }

    /** @param array<string, mixed> $payload */
    public function update(string $id, array $payload): mixed
    {
        return $this->http->request('PUT', $this->basePath . '/' . rawurlencode($id), $payload);
    }

    public function delete(string $id): mixed
    {
        return $this->http->request('DELETE', $this->basePath . '/' . rawurlencode($id));
    }

    /** @param array<string, mixed>|null $payload */
    public function post(string $suffix, ?array $payload = null, ?string $idempotencyKey = null): mixed
    {
        $headers = $idempotencyKey ? ['Idempotency-Key' => $idempotencyKey] : [];

        return $this->http->request(
            'POST',
            rtrim($this->basePath, '/') . '/' . ltrim($suffix, '/'),
            $payload,
            $headers,
        );
    }
}
