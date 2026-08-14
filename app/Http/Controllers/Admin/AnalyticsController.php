<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Analytics\Period;

class AnalyticsController extends Controller
{
    /**
     * Show Google Analytics (Mock) dashboard
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'weekly'); // daily, weekly, monthly

        // ------------------------------------------------------------------
        // GOOGLE ANALYTICS MOCK DATA based on period
        // ------------------------------------------------------------------
        
        $trendLabel = 'Last 7 Days';
        $periodObj = Period::days(7);

        if ($period === 'daily') {
            $periodObj = Period::days(1);
            $trendLabel = 'Today';
        } elseif ($period === 'monthly') {
            $periodObj = Period::days(30);
            $trendLabel = 'Last 30 Days';
        } elseif ($period === 'yearly') {
            $periodObj = Period::months(12);
            $trendLabel = 'Last 12 Months';
        } elseif ($period === 'custom') {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            
            if ($startDate && $endDate) {
                $start = Carbon::parse($startDate);
                $end = Carbon::parse($endDate);
                $periodObj = Period::create($start, $end);
                $trendLabel = $start->format('d M Y') . ' - ' . $end->format('d M Y');
            } else {
                $period = 'weekly'; // fallback
            }
        }

        // ------------------------------------------------------------------
        // GOOGLE ANALYTICS 4 API INTEGRATION
        // ------------------------------------------------------------------
        try {
            // 1. Get Totals
            $totals = \Analytics::get($periodObj, ['activeUsers', 'newUsers', 'totalUsers', 'screenPageViews', 'bounceRate', 'averageSessionDuration'], [])->first();
            
            $ga4MockStats = [
                'active_users' => $totals['activeUsers'] ?? 0,
                'new_users' => $totals['newUsers'] ?? 0,
                'total_users' => $totals['totalUsers'] ?? 0,
                'pageviews' => $totals['screenPageViews'] ?? 0,
                'bounce_rate' => isset($totals['bounceRate']) ? round((float)$totals['bounceRate'] * 100, 1) . '%' : '0%',
                'avg_session' => isset($totals['averageSessionDuration']) ? gmdate("i\m s\s", (int)$totals['averageSessionDuration']) : '0m 0s'
            ];

            // 2. Line Chart
            $chartData = \Analytics::fetchTotalVisitorsAndPageViews($periodObj);
            $ga4Chart = $chartData->map(function ($row) {
                return [
                    'date' => $row['date']->format('d M'),
                    'visitors' => $row['activeUsers'],
                    'pageviews' => $row['screenPageViews']
                ];
            })->toArray();

            // 3. Top Pages
            $pagesData = \Analytics::fetchMostVisitedPages($periodObj, 10);
            $topPages = $pagesData->map(function ($row) {
                return [
                    'path' => $row['fullPageUrl'],
                    'title' => $row['pageTitle'],
                    'views' => $row['screenPageViews']
                ];
            })->toArray();

            // 4. Traffic Sources
            $sourcesData = \Analytics::get($periodObj, ['activeUsers'], ['sessionDefaultChannelGroup']);
            $totalTrafficUsers = $sourcesData->sum('activeUsers') ?: 1;
            $trafficSources = $sourcesData->map(function ($row) use ($totalTrafficUsers) {
                return [
                    'source' => $row['sessionDefaultChannelGroup'],
                    'users' => $row['activeUsers'],
                    'percentage' => round(($row['activeUsers'] / $totalTrafficUsers) * 100)
                ];
            })->toArray();

            // 5. Demographics (Top Cities)
            $citiesData = \Analytics::get($periodObj, ['activeUsers'], ['city'], 10, [ \Spatie\Analytics\OrderBy::metric('activeUsers', true) ]);
            $topCities = $citiesData->map(function ($row) {
                return [
                    'city' => $row['city'],
                    'users' => $row['activeUsers']
                ];
            })->toArray();

            // Top Countries (just in case it's needed)
            $countriesData = \Analytics::get($periodObj, ['activeUsers'], ['country'], 10, [ \Spatie\Analytics\OrderBy::metric('activeUsers', true) ]);
            $topCountries = $countriesData->map(function ($row) {
                return [
                    'country' => $row['country'],
                    'users' => $row['activeUsers']
                ];
            })->toArray();

            // 6. Device Categories
            $deviceData = \Analytics::get($periodObj, ['activeUsers'], ['deviceCategory']);
            $deviceCategories = $deviceData->map(function ($row) {
                $icon = 'fas fa-desktop';
                $color = '#10b981';
                if (strtolower($row['deviceCategory']) === 'mobile') { $icon = 'fas fa-mobile-alt'; $color = '#3b82f6'; }
                if (strtolower($row['deviceCategory']) === 'tablet') { $icon = 'fas fa-tablet-alt'; $color = '#f59e0b'; }
                return [
                    'device' => ucfirst($row['deviceCategory']),
                    'users' => $row['activeUsers'],
                    'icon' => $icon,
                    'color' => $color
                ];
            })->toArray();

            // 7. Browsers
            $browserData = \Analytics::get($periodObj, ['activeUsers'], ['browser'], 5, [ \Spatie\Analytics\OrderBy::metric('activeUsers', true) ]);
            $browsers = $browserData->map(function ($row) {
                $icon = 'fab fa-chrome'; $color = '#4285F4';
                if (strpos(strtolower($row['browser']), 'safari') !== false) { $icon = 'fab fa-safari'; $color = '#000000'; }
                if (strpos(strtolower($row['browser']), 'firefox') !== false) { $icon = 'fab fa-firefox'; $color = '#FF7139'; }
                if (strpos(strtolower($row['browser']), 'edge') !== false) { $icon = 'fab fa-edge'; $color = '#0078D7'; }
                return [
                    'browser' => $row['browser'],
                    'users' => $row['activeUsers'],
                    'icon' => $icon,
                    'color' => $color
                ];
            })->toArray();

            // 8. OS
            $osData = \Analytics::get($periodObj, ['activeUsers'], ['operatingSystem'], 5, [ \Spatie\Analytics\OrderBy::metric('activeUsers', true) ]);
            $os = $osData->map(function ($row) {
                $icon = 'fas fa-laptop'; $color = '#0078D6';
                $name = strtolower($row['operatingSystem']);
                if (strpos($name, 'windows') !== false) { $icon = 'fab fa-windows'; }
                elseif (strpos($name, 'mac') !== false || strpos($name, 'ios') !== false) { $icon = 'fab fa-apple'; $color = '#000000'; }
                elseif (strpos($name, 'android') !== false) { $icon = 'fab fa-android'; $color = '#3DDC84'; }
                elseif (strpos($name, 'linux') !== false) { $icon = 'fab fa-linux'; $color = '#000000'; }
                return [
                    'os' => $row['operatingSystem'],
                    'users' => $row['activeUsers'],
                    'icon' => $icon,
                    'color' => $color
                ];
            })->toArray();

            // 9. E-Commerce Top Products
            $topProductsData = \Analytics::get($periodObj, ['itemsPurchased', 'itemRevenue'], ['itemName'], 5, [ \Spatie\Analytics\OrderBy::metric('itemRevenue', true) ]);
            $topProducts = $topProductsData->map(function ($row) {
                return [
                    'name' => $row['itemName'],
                    'sales' => $row['itemsPurchased'] ?? 0,
                    'revenue' => $row['itemRevenue'] ?? 0
                ];
            })->toArray();

        } catch (\Exception $e) {
            // Fallback empty data if API fails (e.g. SSL issue locally, wrong credentials)
            $ga4MockStats = ['active_users' => 0, 'new_users' => 0, 'total_users' => 0, 'pageviews' => 0, 'bounce_rate' => '0%', 'avg_session' => '0m'];
            $ga4Chart = [];
            $topPages = [];
            $trafficSources = [];
            $topCities = [];
            $topCountries = [];
            $deviceCategories = [];
            $browsers = [];
            $os = [];
            $topProducts = [];
            \Log::error('GA4 API Error: ' . $e->getMessage());
        }

        if ($request->get('export') === 'excel') {
            $filename = "analytics_report_{$period}_" . date('Ymd') . ".csv";
            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function() use($ga4MockStats, $topPages, $topProducts) {
                $file = fopen('php://output', 'w');
                // Traffic Summary
                fputcsv($file, ['METRIK PENGUNJUNG']);
                fputcsv($file, ['Active Users', 'Total Users', 'New Users', 'Pageviews', 'Bounce Rate', 'Avg Session']);
                fputcsv($file, [
                    $ga4MockStats['active_users'],
                    $ga4MockStats['total_users'],
                    $ga4MockStats['new_users'],
                    $ga4MockStats['pageviews'],
                    $ga4MockStats['bounce_rate'],
                    $ga4MockStats['avg_session']
                ]);
                fputcsv($file, []);

                // Top Products
                fputcsv($file, ['PRODUK TERLARIS']);
                fputcsv($file, ['Nama Produk', 'Terjual', 'Pendapatan (Rp)']);
                foreach ($topProducts as $product) {
                    fputcsv($file, [$product['name'], $product['sales'], $product['revenue']]);
                }
                fputcsv($file, []);

                // Top Pages
                fputcsv($file, ['HALAMAN POPULER']);
                fputcsv($file, ['Path', 'Judul', 'Views']);
                foreach ($topPages as $page) {
                    fputcsv($file, [$page['path'], $page['title'], $page['views']]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return view('admin.analytics.index', compact(
            'period',
            'trendLabel',
            'ga4MockStats',
            'ga4Chart',
            'topPages',
            'topProducts',
            'trafficSources',
            'topCountries',
            'topCities',
            'deviceCategories',
            'browsers',
            'os'
        ));
    }

    /**
     * Show Petunjuk Teknis (Juknis) for Dashboard Analytics
     */
    public function guide()
    {
        return view('admin.analytics.guide');
    }
}
