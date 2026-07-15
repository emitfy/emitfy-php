<?php

declare(strict_types=1);

namespace Emitfy;

use Emitfy\Generated\Api\CompaniesApi;
use Emitfy\Generated\Api\WebhooksApi;
use Emitfy\Generated\Configuration as OpenApiConfiguration;
use GuzzleHttp\Client as GuzzleClient;

final class Emitfy
{
    public readonly Webhooks $webhooks;
    public readonly Companies $companies;

    private readonly HttpClient $http;
    private readonly OpenApiConfiguration $openApiConfig;
    private readonly GuzzleClient $guzzle;

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

        $baseUrl = (string) ($config['baseUrl'] ?? 'https://api.emitfy.com/v1');

        $this->http = new HttpClient($apiKey, $apiSecret, $baseUrl);
        $this->webhooks = new Webhooks($this->http);
        $this->companies = new Companies($this->http);

        $this->openApiConfig = OpenApiConfiguration::getDefaultConfiguration()
            ->setHost(rtrim($baseUrl, '/'))
            ->setApiKey('X-Api-Key', $apiKey)
            ->setApiKey('X-Api-Secret', $apiSecret);

        $this->guzzle = new GuzzleClient();
    }

    public function company(string $companyId): CompanyContext
    {
        $companyId = trim($companyId);

        if ($companyId === '') {
            throw new EmitfyException('companyId is required.');
        }

        return new CompanyContext($this->http, $companyId);
    }

    /** Config do client OpenAPI Generator (`Emitfy\Generated\*`). */
    public function openApiConfig(): OpenApiConfiguration
    {
        return $this->openApiConfig;
    }

    /** API tipada de webhooks (OpenAPI). */
    public function webhooksApi(): WebhooksApi
    {
        return new WebhooksApi($this->guzzle, $this->openApiConfig);
    }

    /** API tipada de empresas (OpenAPI). */
    public function companiesApi(): CompaniesApi
    {
        return new CompaniesApi($this->guzzle, $this->openApiConfig);
    }
}
