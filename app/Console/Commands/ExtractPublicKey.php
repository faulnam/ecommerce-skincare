<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExtractPublicKey extends Command
{
    protected $signature = 'paylabs:extract-public-key';
    protected $description = 'Extract correct public key from private key';

    public function handle()
    {
        $privateKeyPath = storage_path('app/paylabs/private-key.pem');
        $publicKeyPath = storage_path('app/paylabs/public-key-correct.pem');

        if (!file_exists($privateKeyPath)) {
            $this->error("Private key not found at: $privateKeyPath");
            return 1;
        }

        // Load private key
        $privateKeyContent = file_get_contents($privateKeyPath);
        $privateKey = openssl_pkey_get_private($privateKeyContent);

        if ($privateKey === false) {
            $this->error("Failed to load private key: " . openssl_error_string());
            return 1;
        }

        // Extract public key
        $publicKeyDetails = openssl_pkey_get_details($privateKey);
        $publicKeyPem = $publicKeyDetails['key'];

        // Save to file
        file_put_contents($publicKeyPath, $publicKeyPem);

        openssl_free_key($privateKey);

        $this->info("✓ Public key extracted successfully!");
        $this->info("✓ Saved to: $publicKeyPath");
        $this->newLine();
        $this->line("Public Key:");
        $this->line($publicKeyPem);
        $this->newLine();
        $this->warn("⚠ IMPORTANT: Submit this public key to Paylabs CS to replace the old one!");

        return 0;
    }
}
