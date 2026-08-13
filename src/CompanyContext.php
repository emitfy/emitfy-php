<?php

declare(strict_types=1);

namespace Emitfy;

final class CompanyContext
{
    public readonly CompanyResource $nfse;
    public readonly CompanyResource $nfe;
    public readonly CompanyResource $nfce;
    public readonly CompanyResource $cte;
    public readonly CompanyResource $customers;
    public readonly CompanyResource $products;
    public readonly InvoicesResource $invoices;
    public readonly CompanyResource $receivedNfes;

    public function __construct(
        private readonly HttpClient $http,
        private readonly string $companyId,
    ) {
        $prefix = '/companies/' . rawurlencode($companyId);
        $this->nfse = new CompanyResource($http, $prefix . '/nfse');
        $this->nfe = new CompanyResource($http, $prefix . '/nfe');
        $this->nfce = new CompanyResource($http, $prefix . '/nfce');
        $this->cte = new CompanyResource($http, $prefix . '/cte');
        $this->customers = new CompanyResource($http, $prefix . '/customers');
        $this->products = new CompanyResource($http, $prefix . '/products');
        $this->invoices = new InvoicesResource($http, $prefix . '/invoices');
        $this->receivedNfes = new CompanyResource($http, $prefix . '/received-nfes');
    }

    public function id(): string
    {
        return $this->companyId;
    }

    /** @param array<string, mixed> $payload */
    public function createCteOs(array $payload, ?string $idempotencyKey = null): mixed
    {
        $headers = $idempotencyKey ? ['Idempotency-Key' => $idempotencyKey] : [];

        return $this->http->request(
            'POST',
            '/companies/' . rawurlencode($this->companyId) . '/cte-os',
            $payload,
            $headers,
        );
    }

    public function status(): mixed
    {
        return $this->http->request('GET', '/companies/' . rawurlencode($this->companyId) . '/status');
    }

    public function setEnvironment(string $environment): mixed
    {
        return $this->http->request(
            'PATCH',
            '/companies/' . rawurlencode($this->companyId) . '/environment',
            ['environment' => $environment],
        );
    }

    public function certificateStatus(): mixed
    {
        return $this->http->request('GET', '/companies/' . rawurlencode($this->companyId) . '/certificate');
    }

    /** @param array<string, mixed> $payload */
    public function uploadCertificate(array $payload): mixed
    {
        return $this->http->request(
            'POST',
            '/companies/' . rawurlencode($this->companyId) . '/certificate',
            $payload,
        );
    }

    public function deleteCertificate(): mixed
    {
        return $this->http->request(
            'DELETE',
            '/companies/' . rawurlencode($this->companyId) . '/certificate',
        );
    }

    /** @param array<string, mixed> $payload */
    public function createCorrectionLetter(string $id, array $payload): mixed
    {
        return $this->http->request(
            'POST',
            '/companies/' . rawurlencode($this->companyId) . '/nfe/' . rawurlencode($id) . '/correction',
            $payload,
        );
    }

    /** @param array<string, mixed> $payload */
    public function inutilizeNfe(array $payload): mixed
    {
        return $this->http->request(
            'POST',
            '/companies/' . rawurlencode($this->companyId) . '/nfe/inutilizations',
            $payload,
        );
    }

    public function transmitNfce(string $id): mixed
    {
        return $this->http->request(
            'POST',
            '/companies/' . rawurlencode($this->companyId) . '/nfce/' . rawurlencode($id) . '/transmit',
            [],
        );
    }

    /** @param array<string, mixed> $payload */
    public function inutilizeNfce(array $payload): mixed
    {
        return $this->http->request(
            'POST',
            '/companies/' . rawurlencode($this->companyId) . '/nfce/inutilizations',
            $payload,
        );
    }
}
