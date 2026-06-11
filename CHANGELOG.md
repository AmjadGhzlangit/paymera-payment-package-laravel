# Changelog

All notable changes to this package will be documented in this file.

## [1.1.0] - 2026-06-11

### Security
- `PaymeraClient` now rejects a non-HTTPS `base_url` at construction, preventing Basic Auth credentials from being sent in cleartext over `http://`. **Breaking:** constructing the client with an `http://` URL now throws a `PaymeraException`.
- All API calls now set a connect timeout (5s) and request timeout (15s) so a slow or unresponsive gateway cannot block the calling process indefinitely.

## [1.0.0] - 2026-06-04

### Added
- `PaymeraClient` with `createPayment`, `getPaymentStatus`, and `cancelPayment` methods
- `CreatePaymentRequest` and `CreatePaymentResult` DTOs
- `PaymentStatusResult` DTO with `isAccepted()`, `isPending()`, `isFailed()`, and `isCanceled()` helpers
- `PaymentStatus` enum (`Pending`, `Accepted`, `Failed`, `Canceled`)
- `PaymeraException`, `UnauthorizedException`, and `PaymentFailedException` exception classes
- `Paymera` facade with IDE-friendly `@method` and `@throws` annotations
- `PaymeraServiceProvider` with Laravel auto-discovery support
- Config file publishable via `vendor:publish --tag=paymera-config`
- Pest test suite covering all three API methods and error codes
