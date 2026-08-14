<?php

use Illuminate\Support\Facades\Route;

Route::get('/extract-public-key', function () {
    $privateKeyPath = storage_path('app/paylabs/private-key.pem');
    $publicKeyPath = storage_path('app/paylabs/public-key-correct.pem');

    // Load private key
    $privateKeyContent = file_get_contents($privateKeyPath);
    $privateKey = openssl_pkey_get_private($privateKeyContent);

    if ($privateKey === false) {
        return response("Failed to load private key: " . openssl_error_string(), 500);
    }

    // Extract public key
    $publicKeyDetails = openssl_pkey_get_details($privateKey);
    $publicKeyPem = $publicKeyDetails['key'];

    // Save to file
    file_put_contents($publicKeyPath, $publicKeyPem);

    openssl_free_key($privateKey);

    return response()->json([
        'success' => true,
        'message' => 'Public key extracted successfully!',
        'saved_to' => $publicKeyPath,
        'public_key' => $publicKeyPem,
    ]);
});
