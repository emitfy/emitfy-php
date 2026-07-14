<?php

declare(strict_types=1);

namespace Emitfy;

final class Emitfy
{
    public readonly Webhooks $webhooks;
    public readonly Companies $companies;

    private readonly HttpClient $http;

    /**
     * @param array{apiKey: string, apiSecret: string, baseUrl?: string} $config
     */
    public function __construct(array $config)
    {
        $apiKey = trim((string) ($config['apiKey'] ?? ''));
        $apiSecret = trim((string) ($config['apiSecret'] ?? ''));

        if ($apiKey === '' || $apiSecret === '') {
            throw new EmitfyException('apiKey and apiSecret are required.');
        }

        $this->http = new HttpClient(
            $apiKey,
            $apiSecret,
            (string) ($config['baseUrl'] ?? 'https://api.emitfy.com/v1'),
        );
        $this->webhooks = new Webhooks($this->http);
        $this->companies = new Companies($this->http);
    }

    public function company(string $companyId): CompanyContext
    {
        $companyId = trim($companyId);

        if ($companyId === '') {
            throw new EmitfyException('companyId is required.');
        }

        return new CompanyContext($this->http, $companyId);
    }
}
