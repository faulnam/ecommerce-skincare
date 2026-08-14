<?php

namespace App\Console\Commands;

use App\Services\BiteshipService;
use Illuminate\Console\Command;

class CheckBiteshipBalance extends Command
{
    protected $signature = 'biteship:check-balance';
    protected $description = 'Check Biteship account balance';

    public function handle(BiteshipService $biteship)
    {
        $this->info('Checking Biteship balance...');

        try {
            $response = \Http::withHeaders([
                'Authorization' => config('biteship.api_key'),
                'Content-Type' => 'application/json',
            ])->get(config('biteship.base_url') . '/v1/balance');

            if ($response->successful()) {
                $data = $response->json();
                
                $this->info("\n✓ Biteship Account Info:");
                $this->table(
                    ['Field', 'Value'],
                    [
                        ['Balance', number_format($data['balance'] ?? 0, 0, ',', '.') . ' Pts'],
                        ['Status', $data['status'] ?? 'N/A'],
                    ]
                );

                $balance = $data['balance'] ?? 0;
                
                if ($balance < 10000) {
                    $this->warn("\n⚠️  Saldo rendah! Minimal 10,000 Pts untuk operasional lancar.");
                    $this->line("   Top-up di: https://biteship.com");
                }
            } else {
                $this->error('Failed to get balance: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }

        return 0;
    }
}
