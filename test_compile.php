<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$content = $response->getContent();
if (strpos($content, "window.location.href = '/login'") !== false) {
    echo "GUEST REDIRECT IS IN HTML\n";
} else {
    echo "GUEST REDIRECT IS MISSING\n";
}
