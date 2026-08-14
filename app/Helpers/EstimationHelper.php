<?php

namespace App\Helpers;

use Carbon\Carbon;

class EstimationHelper
{
    /**
     * Detect zone based on origin and destination
     */
    public static function detectZone($originCity, $originProvince, $destCity, $destProvince)
    {
        if (strtolower($originCity) === strtolower($destCity)) {
            return 'same_city';
        }
        
        if (strtolower($originProvince) === strtolower($destProvince)) {
            return 'same_province';
        }
        
        $javaIsland = ['jawa timur', 'jawa tengah', 'jawa barat', 'dki jakarta', 'banten', 'yogyakarta'];
        $sumatraIsland = ['sumatera utara', 'sumatera barat', 'sumatera selatan', 'riau', 'jambi', 'bengkulu', 'lampung', 'aceh', 'kepulauan riau', 'bangka belitung'];
        $kalimantanIsland = ['kalimantan timur', 'kalimantan barat', 'kalimantan selatan', 'kalimantan tengah', 'kalimantan utara'];
        $sulawesiIsland = ['sulawesi utara', 'sulawesi tengah', 'sulawesi selatan', 'sulawesi tenggara', 'gorontalo', 'sulawesi barat'];
        
        $originProvLower = strtolower($originProvince);
        $destProvLower = strtolower($destProvince);
        
        if ((in_array($originProvLower, $javaIsland) && in_array($destProvLower, $javaIsland)) ||
            (in_array($originProvLower, $sumatraIsland) && in_array($destProvLower, $sumatraIsland)) ||
            (in_array($originProvLower, $kalimantanIsland) && in_array($destProvLower, $kalimantanIsland)) ||
            (in_array($originProvLower, $sulawesiIsland) && in_array($destProvLower, $sulawesiIsland))) {
            return 'same_island';
        }
        
        return 'different_island';
    }
    
    /**
     * Adjust ETD based on zone
     */
    public static function adjustETD($etd, $zone, $serviceType)
    {
        if (preg_match('/(\d+)-(\d+)/', $etd, $matches)) {
            $minDays = (int) $matches[1];
            $maxDays = (int) $matches[2];
        } elseif (preg_match('/(\d+)/', $etd, $matches)) {
            $minDays = (int) $matches[1];
            $maxDays = $minDays;
        } else {
            $minDays = 2;
            $maxDays = 3;
        }
        
        if (in_array($serviceType, ['instant', 'sameday'])) {
            return [$minDays, $maxDays];
        }
        
        switch ($zone) {
            case 'same_city':
                break;
            case 'same_province':
                $maxDays += 1;
                break;
            case 'same_island':
                $minDays += 1;
                $maxDays += 2;
                break;
            case 'different_island':
                $minDays += 2;
                $maxDays += 4;
                break;
        }
        
        return [$minDays, $maxDays];
    }
    
    /**
     * Convert days to date range
     */
    public static function convertToDateRange($minDays, $maxDays)
    {
        $startDate = Carbon::now()->addDays($minDays);
        $endDate = Carbon::now()->addDays($maxDays);
        
        if ($startDate->month === $endDate->month) {
            return $startDate->format('d') . ' – ' . $endDate->format('d F');
        } else {
            return $startDate->format('d M') . ' – ' . $endDate->format('d M');
        }
    }
    
    /**
     * Format ETD text
     */
    public static function formatETDText($minDays, $maxDays, $serviceType)
    {
        if ($serviceType === 'instant') {
            return '1–3 jam';
        }
        
        if ($serviceType === 'sameday') {
            return '6–8 jam (hari yang sama)';
        }
        
        if ($minDays === $maxDays) {
            return $minDays . ' hari';
        }
        
        return $minDays . '–' . $maxDays . ' hari';
    }
    
    /**
     * Check if service is available for zone
     */
    public static function isServiceAvailable($serviceType, $zone)
    {
        if ($serviceType === 'instant' && $zone !== 'same_city') {
            return false;
        }
        
        if ($serviceType === 'sameday' && !in_array($zone, ['same_city', 'same_province'])) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Get zone label
     */
    public static function getZoneLabel($zone)
    {
        return match($zone) {
            'same_city' => 'Dalam Kota',
            'same_province' => 'Dalam Provinsi',
            'same_island' => 'Antar Provinsi (1 Pulau)',
            'different_island' => 'Antar Pulau',
            default => 'Unknown'
        };
    }
}
