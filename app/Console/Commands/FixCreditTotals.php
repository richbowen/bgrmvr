<?php

namespace App\Console\Commands;

use App\Models\UserCredit;
use Illuminate\Console\Command;

class FixCreditTotals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'credits:fix-totals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix credit total calculations for all users based on actual usage';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Fixing credit totals for all users...');

        $credits = UserCredit::with('user')->get();
        $fixedCount = 0;

        foreach ($credits as $userCredit) {
            $oldUsed = $userCredit->total_used;
            $oldPurchased = $userCredit->total_purchased;

            $userCredit->recalculateTotals();

            $newUsed = $userCredit->fresh()->total_used;
            $newPurchased = $userCredit->fresh()->total_purchased;

            if ($oldUsed !== $newUsed || $oldPurchased !== $newPurchased) {
                $this->line("Fixed user {$userCredit->user->email}: Used {$oldUsed}→{$newUsed}, Purchased {$oldPurchased}→{$newPurchased}");
                $fixedCount++;
            }
        }

        $this->info("Fixed credit totals for {$fixedCount} users.");

        return Command::SUCCESS;
    }
}
