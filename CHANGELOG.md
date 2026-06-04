# Changelog

All notable changes to this package will be documented in this file.

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
