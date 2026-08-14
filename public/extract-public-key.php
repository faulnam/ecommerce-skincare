<?php

$privateKeyPath = __DIR__ . '/../storage/app/paylabs/private-key.pem';
$publicKeyPath = __DIR__ . '/../storage/app/paylabs/public-key-correct.pem';

if (!file_exists($privateKeyPath)) {
    die("Private key not found at: $privateKeyPath");
}

// Load private key
$privateKeyContent = file_get_contents($privateKeyPath);
$privateKey = openssl_pkey_get_private($privateKeyContent);

if ($privateKey === false) {
    die("Failed to load private key: " . openssl_error_string());
}

// Extract public key
$publicKeyDetails = openssl_pkey_get_details($privateKey);
$publicKeyPem = $publicKeyDetails['key'];

// Save to file
file_put_contents($publicKeyPath, $publicKeyPem);

openssl_free_key($privateKey);

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Public key extracted successfully!',
    'saved_to' => $publicKeyPath,
    'public_key' => $publicKeyPem,
], JSON_PRETTY_PRINT);
