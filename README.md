# emitfy/sdk (PHP)

SDK oficial da API Emitfy para PHP.

```bash
composer require emitfy/sdk
```

```php
use Emitfy\Emitfy;

$emitfy = new Emitfy([
  'apiKey' => getenv('EMITFY_API_KEY'),
  'apiSecret' => getenv('EMITFY_API_SECRET'),
]);

$emitfy->webhooks->create([
  'url' => 'https://seu-sistema.com/webhooks/emitfy',
  'events' => ['invoice' => ['nfse.authorized'], 'cte' => []],
]);

$company = $emitfy->company(getenv('EMITFY_COMPANY_ID'));
$company->nfse->create([
  'serviceDescription' => 'Serviço de software',
  'amount' => 100,
]);
```

Docs: https://api.emitfy.com/docs/sdks
