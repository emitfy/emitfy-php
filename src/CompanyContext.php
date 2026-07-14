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
    public readonly CompanyResource $sales;
    public readonly CompanyResource $invoices;
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
        $this->sales = new CompanyResource($http, $prefix . '/sales');
        $this->invoices = new CompanyResource($http, $prefix . '/invoices');
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
}
