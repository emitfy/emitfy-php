# emitfy/sdk

SDK oficial da API Emitfy para PHP (tipado via OpenAPI).

## Install

```bash
composer require emitfy/sdk
```

## Uso (facade)

```php
<?php
use Emitfy\Emitfy;
use Emitfy\Generated\Model\WebhookCreate;

$emitfy = new Emitfy([
  'apiKey' => getenv('EMITFY_API_KEY'),
  'apiSecret' => getenv('EMITFY_API_SECRET'),
]);

$company = $emitfy->company(getenv('EMITFY_COMPANY_ID'));
$company->nfse->create(['serviceDescription' => 'Serviço', 'amount' => 100], 'pedido-001');
```

## Tipagem OpenAPI

Modelos e APIs tipados em `Emitfy\Generated\*`:

```php
$api = $emitfy->webhooksApi();
$body = new WebhookCreate();
$body->setUrl('https://seu-sistema.com/webhooks/emitfy');
$api->webhooksCreate($body);
```

Docs: https://api.emitfy.com/docs/sdks  
OpenAPI: https://api.emitfy.com/openapi.yaml
