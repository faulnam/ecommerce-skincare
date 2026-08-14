<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\PointTransaction;
use Illuminate\Console\Command;

class ExpirePointsCommand extends Command
{
    protected $signature = 'points:expire';
    protected $description = 'Expire point transactions that have passed their expiry date and sync user balances';

    public function handle(): int
    {
        // Mark expired transactions by consuming all remaining available points
        $expiredCount = 0;
        PointTransaction::earnOrBonus()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->whereRaw('(points - consumed) > 0')
            ->chunkById(200, function ($transactions) use (&$expiredCount) {
                foreach ($transactions as $transaction) {
                    $transaction->consumed = $transaction->points;
                    $transaction->save();
                    $expiredCount++;
                }
            });

        $this->info("Expired {$expiredCount} point transactions.");

        // Sync user balances for affected users
        $affectedUserIds = PointTransaction::earnOrBonus()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->distinct()
            ->pluck('user_id');

        foreach ($affectedUserIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                $user->syncPoints();
            }
        }

        $this->info("Synced balances for {$affectedUserIds->count()} users.");

        return self::SUCCESS;
    }
}
