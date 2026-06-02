# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Install dependencies
composer install

# Run all tests
./vendor/bin/pest tests/

# Run a single test file
./vendor/bin/pest tests/Feature/CreatePaymentTest.php

# Run a specific test by description
./vendor/bin/pest --filter="creates payment"
```

## Architecture

This is a standalone Laravel package (`amjad-gh/paymera-payment-laravel`) that wraps the [Paymera eGate](https://paymera.cc) payment gateway API. Namespace: `Casper\Paymera`.

### Request / Response Flow

All HTTP calls go through `PaymeraClient`, which uses Laravel's `Http` facade with HTTP Basic Auth. Every API response follows the shape `{ ErrorCode, ErrorMessage, Data }`. The client calls `handleErrorCode()` to map error codes to typed exceptions before returning a DTO:

- `ErrorCode 0` → success, parse `Data` into a result DTO
- `ErrorCode 1` → `UnauthorizedException`
- `ErrorCode 100` → `PaymentFailedException`
- any other code → `PaymeraException` (base class, carries raw error code)

### Service Container Binding

`PaymeraServiceProvider` registers `PaymeraClient` as a singleton under the key `'paymera'`, resolved from `config('paymera')`. The `Paymera` facade proxies to this singleton. Config is merged (not replaced) via `mergeConfigFrom`, so host apps that don't publish the config file still get defaults.

### DTOs

- `CreatePaymentRequest` — input DTO with a `toArray()` method that omits `null` optional fields before posting
- `CreatePaymentResult` — output with `paymentId` and `redirectUrl` (maps `url` key from API)
- `PaymentStatusResult` — output with a `PaymentStatus` enum and helper methods (`isAccepted()`, `isPending()`, `isFailed()`, `isCanceled()`)

### API Endpoints

| Method | Path | Operation |
|--------|------|-----------|
| POST | `/api/create-payment` | Create payment |
| GET | `/api/get-payment-status/{paymentId}` | Get status |
| POST | `/api/cancel-payment` | Cancel payment (sends `lang` + `payment_id`) |

### Testing

Tests use [Pest](https://pestphp.com/) with [Orchestra Testbench](https://github.com/orchestral/testbench). `tests/Pest.php` bootstraps the package via `getPackageProviders()` and `getPackageAliases()`. All tests mock HTTP using `Http::fake()` — no real API calls are made. Tests instantiate `PaymeraClient` directly (not via facade) with a local `$config` array.
