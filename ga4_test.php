<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;

try {
    $period = Period::days(7);
    $totals = Analytics::get(
        $period,
        ['activeUsers', 'newUsers', 'totalUsers', 'screenPageViews', 'bounceRate', 'averageSessionDuration'],
        ['date']
    );
    print_r($totals);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
