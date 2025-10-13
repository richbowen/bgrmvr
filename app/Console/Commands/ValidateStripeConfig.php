<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stripe\StripeClient;

class ValidateStripeConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stripe:validate-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate Stripe configuration and price IDs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Validating Stripe Configuration...');
        $this->newLine();

        // Check if Stripe keys are configured
        if (! config('cashier.key') || ! config('cashier.secret')) {
            $this->error('❌ Stripe keys are not configured. Please set STRIPE_KEY and STRIPE_SECRET in your .env file.');

            return 1;
        }

        $this->info('✅ Stripe keys are configured');

        try {
            $stripe = new StripeClient(config('cashier.secret'));

            // Test API connection
            $stripe->products->all(['limit' => 1]);
            $this->info('✅ Stripe API connection successful');

            // Validate subscription price IDs
            $this->info('📋 Validating subscription price IDs...');
            $subscriptionTiers = config('stripe.subscription_tiers');

            foreach ($subscriptionTiers as $tier) {
                try {
                    $price = $stripe->prices->retrieve($tier['stripe_price_id']);
                    $unitAmount = $price->unit_amount / 100;
                    $interval = $price->recurring->interval;
                    $this->info("  ✅ {$tier['name']}: {$tier['stripe_price_id']} (\${$unitAmount}/{$interval})");
                } catch (\Exception $e) {
                    $this->error("  ❌ {$tier['name']}: {$tier['stripe_price_id']} - {$e->getMessage()}");
                }
            }

            // Validate credit package price IDs
            $this->info('💳 Validating credit package price IDs...');
            $creditPackages = config('stripe.credit_packages');

            foreach ($creditPackages as $package) {
                try {
                    $price = $stripe->prices->retrieve($package['stripe_price_id']);
                    $unitAmount = $price->unit_amount / 100;
                    $this->info("  ✅ {$package['name']}: {$package['stripe_price_id']} (\${$unitAmount})");
                } catch (\Exception $e) {
                    $this->error("  ❌ {$package['name']}: {$package['stripe_price_id']} - {$e->getMessage()}");
                }
            }

            $this->newLine();
            $this->info('🎉 Stripe configuration validation complete!');

        } catch (\Exception $e) {
            $this->error("❌ Stripe API error: {$e->getMessage()}");

            return 1;
        }

        return 0;
    }
}
