<?php
// Clear cache helper
echo "Clearing cache...<br>";

// Clear config cache
if (file_exists(__DIR__ . '/../bootstrap/cache/config.php')) {
    unlink(__DIR__ . '/../bootstrap/cache/config.php');
    echo "✓ Config cache cleared<br>";
}

// Clear route cache
if (file_exists(__DIR__ . '/../bootstrap/cache/routes-v7.php')) {
    unlink(__DIR__ . '/../bootstrap/cache/routes-v7.php');
    echo "✓ Route cache cleared<br>";
}

// Clear view cache
$viewPath = __DIR__ . '/../storage/framework/views';
if (is_dir($viewPath)) {
    $files = glob($viewPath . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "✓ View cache cleared<br>";
}

echo "<br>Done! <a href='/customer/checkout'>Go to Checkout</a>";
