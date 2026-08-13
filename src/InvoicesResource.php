<?php

declare(strict_types=1);

namespace Emitfy;

final class InvoicesResource extends CompanyResource
{
    /** @param array<string, mixed> $payload */
    public function update(string $id, array $payload): mixed
    {
        return $this->http->request('PATCH', $this->basePath . '/' . rawurlencode($id), $payload);
    }

    public function emit(string $id): mixed
    {
        return $this->http->request('POST', $this->basePath . '/' . rawurlencode($id) . '/emit', []);
    }

    /** @param array<string, mixed>|null $payload */
    public function cancel(string $id, ?array $payload = null): mixed
    {
        return $this->http->request(
            'POST',
            $this->basePath . '/' . rawurlencode($id) . '/cancel',
            $payload ?? [],
        );
    }

    public function consult(string $id): mixed
    {
        return $this->http->request('GET', $this->basePath . '/' . rawurlencode($id) . '/consult');
    }

    public function events(string $id): mixed
    {
        return $this->http->request('GET', $this->basePath . '/' . rawurlencode($id) . '/events');
    }

    /** @param array<string, mixed>|null $payload */
    public function sendEmail(string $id, ?array $payload = null): mixed
    {
        return $this->http->request(
            'POST',
            $this->basePath . '/' . rawurlencode($id) . '/send-borrower-email',
            $payload ?? [],
        );
    }
}
