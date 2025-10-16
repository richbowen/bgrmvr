# Stripe API Tests Configuration

## Problem

Some tests were failing on CI environments because they required Stripe API credentials that weren't available or necessary in those environments.

## Solution

Added `beforeEach()` blocks to tests that make actual Stripe API calls to skip them when credentials aren't configured.

## Files Modified

### `tests/Feature/UserSubscriptionStatusTest.php`

-   Added check for `config('cashier.secret')` and `config('cashier.key')`
-   Skips tests that call `createAsStripeCustomer()` and `newSubscription()`
-   These tests make real API calls to create Stripe customers and subscriptions

### `tests/Feature/TrialSignupTest.php`

-   Added same Stripe credential check
-   Skips tests that redirect to `checkout.stripe.com`
-   These tests trigger Stripe checkout session creation

### `tests/Feature/SubscriptionCreditTest.php`

-   Added Stripe credential and configuration checks
-   Skips tests that depend on actual Stripe price IDs being configured
-   Tests webhook handling and credit logic that requires real Stripe configuration

## Test Behavior

### When Stripe credentials are available:

-   All tests run normally
-   Real API calls are made to Stripe
-   Full integration testing occurs

### When Stripe credentials are missing:

-   Tests are automatically skipped with warning message: "Stripe credentials not configured"
-   No API calls are attempted
-   CI/CD pipelines won't fail due to missing credentials

## Environment Variables Checked

-   `STRIPE_KEY` (public key)
-   `STRIPE_SECRET` (secret key)
-   `STRIPE_STARTER_PRICE_ID`, `STRIPE_BASIC_PRICE_ID`, etc. (actual Stripe price IDs)

These are mapped to Laravel Cashier config:

-   `config('cashier.key')`
-   `config('cashier.secret')`
-   `config('stripe.subscription_tiers')` with real price IDs

## Running Tests

```bash
# Run with Stripe credentials (normal development)
php artisan test tests/Feature/UserSubscriptionStatusTest.php

# Run without Stripe credentials (CI environment)
STRIPE_KEY= STRIPE_SECRET= php artisan test tests/Feature/UserSubscriptionStatusTest.php

# The tests will be skipped automatically if credentials are missing
```

This ensures that your CI pipeline won't fail due to missing Stripe credentials while still allowing full integration testing in environments where Stripe is properly configured.
