# Paymera Laravel

[![PHP](https://img.shields.io/badge/PHP-%5E8.1-blue)](https://www.php.net)
[![Laravel](https://img.shields.io/badge/Laravel-10.x%20%7C%2011.x%20%7C%2012.x-red)](https://laravel.com)
[![Packagist](https://img.shields.io/packagist/v/amjad-gh/paymera-payment-laravel)](https://packagist.org/packages/amjad-gh/paymera-payment-laravel)
[![License](https://img.shields.io/badge/license-MIT-blue)](LICENSE)

Laravel package for integrating with the [Paymera eGate](https://paymera.cc) payment gateway API.

---

## Requirements

- PHP ^8.1
- Laravel ^10.0 | ^11.0 | ^12.0

---

## Installation

```bash
composer require amjad-gh/paymera-payment-laravel
```

The service provider and facade are auto-discovered by Laravel.

### Publish the config file

```bash
php artisan vendor:publish --tag=paymera-config
```

---

## Configuration

Add the following variables to your `.env` file:

```env
PAYMERA_BASE_URL=https://egate-t.paymera.cc   # test environment
# PAYMERA_BASE_URL=https://egate.paymera.cc   # production environment

PAYMERA_USERNAME=your-username
PAYMERA_PASSWORD=your-password
PAYMERA_TERMINAL_ID=your-terminal-id
PAYMERA_LANG=en
```

> **Security:** credentials are sent via HTTP Basic Auth on the server side only and are never exposed to the client.

The published config file (`config/paymera.php`) contains:

```php
return [
    'base_url'    => env('PAYMERA_BASE_URL', 'https://egate-t.paymera.cc'),
    'username'    => env('PAYMERA_USERNAME'),
    'password'    => env('PAYMERA_PASSWORD'),
    'terminal_id' => env('PAYMERA_TERMINAL_ID'),
    'lang'        => env('PAYMERA_LANG', 'en'),
];
```

---

## Usage

### Create a Payment

```php
use Casper\Paymera\Facades\Paymera;
use Casper\Paymera\DTOs\CreatePaymentRequest;

$result = Paymera::createPayment(new CreatePaymentRequest(
    amount:      5000,                                             // amount in smallest currency unit
    callbackURL: 'https://yourapp.com/orders/1234/payment/return', // see note below
    triggerURL:  'https://yourapp.com/payment/webhook',            // server-to-server notification
    terminalId:  config('paymera.terminal_id'),
    lang:        config('paymera.lang'),
    notes:       'Order #1234',                                    // optional
));

// $result->paymentId  — unique payment identifier
// $result->redirectUrl — redirect the user here to complete payment

return redirect($result->redirectUrl);
```

> **`callbackURL`** is the URL the gateway redirects the user to when they click **Cancel** or **Close** after the payment flow. Include the merchant order identifier in the URL so your landing page can identify which order is returning (e.g. `/orders/1234/payment/return`).

### Get Payment Status

```php
use Casper\Paymera\Facades\Paymera;

$status = Paymera::getPaymentStatus($paymentId);

if ($status->isAccepted()) {
    // payment succeeded
}

if ($status->isPending()) {
    // still waiting
}

if ($status->isFailed()) {
    // payment failed
}

if ($status->isCanceled()) {
    // payment was canceled
}

// Raw enum value:  $status->status  (PaymentStatus enum)
// Other fields:    $status->rrn, $status->amount, $status->terminalId,
//                  $status->creationTimestamp, $status->notes
```

### Cancel a Payment

```php
use Casper\Paymera\Facades\Paymera;

Paymera::cancelPayment($paymentId);
```

---

## API Environments

| Environment | Base URL |
|-------------|----------|
| Test        | `https://egate-t.paymera.cc` |
| Production  | `https://egate.paymera.cc`   |

Switch environments by changing `PAYMERA_BASE_URL` in your `.env`.

---

## DTOs

### `CreatePaymentRequest`

| Parameter     | Type          | Required | Default | Description |
|---------------|---------------|----------|---------|-------------|
| `amount`      | `int`         | ✓        |         | Amount in smallest currency unit |
| `callbackURL` | `string`      | ✓        |         | URL the gateway redirects the user to when they click **Cancel** or **Close** after payment. Should include the merchant order identifier so the landing page can determine which order it belongs to (e.g. `https://yourapp.com/orders/1234/payment/return`) |
| `triggerURL`  | `string`      | ✓        |         | Server-to-server webhook URL |
| `terminalId`  | `string`      | ✓        |         | Your terminal ID |
| `lang`        | `string`      | ✓        |         | Language code (e.g. `en`) |
| `notes`       | `string\|null` |          | `null`  | Optional payment notes |
| `savedCards`  | `int`         |          | `0`     | Enable saved cards (1 = yes) |
| `appUser`     | `string\|null` |          | `null`  | Optional app user identifier |

### `CreatePaymentResult`

| Property      | Type     | Description |
|---------------|----------|-------------|
| `paymentId`   | `string` | Unique payment ID |
| `redirectUrl` | `string` | URL to redirect the user to |

### `PaymentStatusResult`

| Property             | Type            | Description |
|----------------------|-----------------|-------------|
| `status`             | `PaymentStatus`  | Enum: `Pending`, `Accepted`, `Failed`, `Canceled` |
| `rrn`                | `string\|null`   | Reference retrieval number |
| `amount`             | `int`            | Payment amount |
| `terminalId`         | `string\|null`   | Terminal ID |
| `creationTimestamp`  | `string\|null`   | ISO 8601 creation time |
| `notes`              | `string\|null`   | Payment notes |

---

## PaymentStatus Enum

```php
use Casper\Paymera\Enums\PaymentStatus;

PaymentStatus::Pending  // 'P'
PaymentStatus::Accepted // 'A'
PaymentStatus::Failed   // 'F'
PaymentStatus::Canceled // 'C'
```

---

## Exception Handling

All exceptions extend `PaymeraException` which extends `RuntimeException`.

```php
use Casper\Paymera\Exceptions\PaymeraException;
use Casper\Paymera\Exceptions\UnauthorizedException;
use Casper\Paymera\Exceptions\PaymentFailedException;

try {
    $result = Paymera::createPayment($request);
} catch (UnauthorizedException $e) {
    // Invalid credentials — error code 1
} catch (PaymentFailedException $e) {
    // Payment failed — error code 100
} catch (PaymeraException $e) {
    // Any other Paymera error
    $e->getErrorCode();  // raw error code from API
    $e->getMessage();    // error message from API
}
```

---

## Testing

The package ships with a Pest test suite. From the package root:

```bash
composer install
./vendor/bin/pest tests/
```

In your own application tests, use `Http::fake()` to mock the gateway:

```php
use Illuminate\Support\Facades\Http;

Http::fake([
    '*/api/create-payment' => Http::response([
        'ErrorCode'    => 0,
        'ErrorMessage' => 'Success',
        'Data'         => [
            'paymentId' => 'pay_test123',
            'url'       => 'https://egate-t.paymera.cc/pay/pay_test123',
        ],
    ]),
]);
```

---

## License

MIT
