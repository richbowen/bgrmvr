# BGRMVR - Background Removal Service

Professional background removal service built with Laravel and Livewire.

## Stripe Configuration

This application uses Stripe for payment processing. Follow these steps to set up Stripe:

### 1. Create Stripe Account

-   Sign up at [stripe.com](https://stripe.com)
-   Get your API keys from the [API Keys page](https://dashboard.stripe.com/apikeys)

### 2. Environment Variables

Copy the API keys to your `.env` file:

```bash
STRIPE_KEY=pk_test_your_publishable_key_here
STRIPE_SECRET=sk_test_your_secret_key_here
```

### 3. Create Stripe Products

#### Subscription Plans

Create these recurring products in your Stripe Dashboard:

1. **Starter Plan** - $3/month

    - 50 background removals per month
    - Copy the price ID to `STRIPE_STARTER_PRICE_ID`

2. **Basic Plan** - $7/month

    - 150 background removals per month
    - Copy the price ID to `STRIPE_BASIC_PRICE_ID`

3. **Professional Plan** - $15/month

    - 400 background removals per month
    - Copy the price ID to `STRIPE_PROFESSIONAL_PRICE_ID`

4. **Business Plan** - $35/month
    - 1000 background removals per month
    - Copy the price ID to `STRIPE_BUSINESS_PRICE_ID`

#### Credit Packages

Create these one-time payment products:

1. **10 Credits** - $2.00
2. **25 Credits** - $4.00
3. **50 Credits** - $7.00
4. **100 Credits** - $12.00

### 4. Validate Configuration

Run the validation command to ensure everything is set up correctly:

```bash
php artisan stripe:validate-config
```

### 5. Webhooks (Production)

For production, set up webhooks in your Stripe Dashboard:

-   Endpoint: `https://yourdomain.com/stripe/webhook`
-   Events: `invoice.payment_succeeded`, `customer.subscription.deleted`
-   Copy the webhook secret to `STRIPE_WEBHOOK_SECRET`

## Development Setup

1. Clone the repository
2. Install dependencies: `composer install && npm install`
3. Copy `.env.example` to `.env`
4. Generate app key: `php artisan key:generate`
5. Set up your database and run migrations: `php artisan migrate`
6. Configure Stripe (see above)
7. Start development server: `php artisan serve`

## Features

-   Professional background removal
-   Credit-based pricing system
-   Subscription plans
-   One-time credit purchases
-   Usage history and analytics
-   Responsive dashboard
-   Secure file handling

## Tech Stack

-   Laravel 12
-   Livewire 3
-   Flux UI Components
-   Tailwind CSS
-   Stripe/Laravel Cashier
-   SQLite/MySQL
