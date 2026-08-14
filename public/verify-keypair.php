<?php

$privateKeyPath = __DIR__ . '/../storage/app/paylabs/private-key.pem';
$publicKeyPath = __DIR__ . '/../storage/app/paylabs/public-key.pem';

echo "=== Paylabs Key Pair Verification ===\n\n";

// Load private key
$privateKeyContent = file_get_contents($privateKeyPath);
$privateKey = openssl_pkey_get_private($privateKeyContent);

if ($privateKey === false) {
    die("❌ Failed to load private key: " . openssl_error_string() . "\n");
}

// Extract public key from private key
$privateKeyDetails = openssl_pkey_get_details($privateKey);
$extractedPublicKey = $privateKeyDetails['key'];

// Load existing public key file
$existingPublicKey = file_get_contents($publicKeyPath);

// Normalize for comparison (remove whitespace differences)
$extractedNormalized = preg_replace('/\s+/', '', $extractedPublicKey);
$existingNormalized = preg_replace('/\s+/', '', $existingPublicKey);

echo "Private Key Path: $privateKeyPath\n";
echo "Public Key Path: $publicKeyPath\n\n";

echo "Private Key Modulus (first 50 chars):\n";
echo substr(base64_encode($privateKeyDetails['rsa']['n']), 0, 50) . "...\n\n";

if ($extractedNormalized === $existingNormalized) {
    echo "✅ SUCCESS: Private key and public key are MATCHED!\n\n";
    echo "Public Key:\n";
    echo $existingPublicKey . "\n";
    
    // Test signature
    $testData = "TEST:DATA:FOR:SIGNATURE";
    openssl_sign($testData, $signature, $privateKey, OPENSSL_ALGO_SHA256);
    $signatureBase64 = base64_encode($signature);
    
    // Verify with public key
    $publicKeyResource = openssl_pkey_get_public($existingPublicKey);
    $verifyResult = openssl_verify($testData, $signature, $publicKeyResource, OPENSSL_ALGO_SHA256);
    
    if ($verifyResult === 1) {
        echo "✅ Signature verification: PASSED\n";
        echo "Test signature: " . substr($signatureBase64, 0, 50) . "...\n";
    } else {
        echo "❌ Signature verification: FAILED\n";
    }
    
    openssl_free_key($publicKeyResource);
} else {
    echo "❌ ERROR: Private key and public key DO NOT MATCH!\n\n";
    echo "Expected Public Key (from private key):\n";
    echo $extractedPublicKey . "\n\n";
    echo "Current Public Key (in file):\n";
    echo $existingPublicKey . "\n";
}

openssl_free_key($privateKey);
