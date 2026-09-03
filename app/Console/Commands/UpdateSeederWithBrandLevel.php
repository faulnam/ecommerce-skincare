<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpdateSeederWithBrandLevel extends Command
{
    protected $signature = 'seeder:update-brand-level';
    protected $description = 'Update ShopeeProductsSeeder with brand and level data';

    public function handle()
    {
        $seederFile = database_path('seeders/ShopeeProductsSeeder.php');
        $backupFile = database_path('seeders/ShopeeProductsSeeder.php.backup');

        // Create backup
        File::copy($seederFile, $backupFile);
        $this->info('Backup created at: ' . $backupFile);

        $content = File::get($seederFile);

        // Function to determine brand based on product name
        $getBrand = function($name) {
            $nameLower = strtolower($name);
            
            if (strpos($nameLower, 'bullskincare') !== false) {
                return 'Bullskincare';
            } elseif (strpos($nameLower, 'babolat') !== false) {
                return 'Babolat';
            } elseif (strpos($nameLower, 'nox') !== false) {
                return 'Nox';
            } elseif (strpos($nameLower, 'alpha') !== false) {
                return 'Alpha';
            } elseif (strpos($nameLower, 'zephyr') !== false) {
                return 'Zephyr';
            } elseif (strpos($nameLower, 'arronax') !== false) {
                return 'Arronax';
            } elseif (strpos($nameLower, 'head') !== false && strpos($nameLower, 'zephyr') !== false) {
                return 'Zephyr';
            } elseif (strpos($nameLower, 'skull') !== false) {
                return null; // Not in the list
            } elseif (strpos($nameLower, 'tactical') !== false) {
                return null; // Not in the list
            } elseif (strpos($nameLower, 'starvie') !== false) {
                return null; // Not in the list
            } elseif (strpos($nameLower, 'hirostar') !== false) {
                return null; // Not in the list
            } elseif (strpos($nameLower, 'joma') !== false) {
                return null; // Not in the list
            } elseif (strpos($nameLower, 'wilson') !== false) {
                return null; // Not in the list
            } elseif (strpos($nameLower, 'odea') !== false) {
                return null; // Not in the list
            } elseif (strpos($nameLower, 'edge') !== false) {
                return null; // Not in the list
            }
            
            return null;
        };

        // Function to determine level based on product name/description
        $getLevel = function($name, $description) {
            $textLower = strtolower($name . ' ' . $description);
            
            // Beginner level indicators
            if (strpos($textLower, 'junior') !== false ||
                strpos($textLower, 'beginner') !== false ||
                strpos($textLower, 'easy to control') !== false ||
                strpos($textLower, 'entry') !== false ||
                strpos($textLower, 'recreational') !== false ||
                strpos($textLower, 'x-zero') !== false) {
                return 'beginner';
            }
            
            // Pro level indicators
            if (strpos($textLower, 'pro') !== false ||
                strpos($textLower, 'expert') !== false ||
                strpos($textLower, 'premier') !== false ||
                strpos($textLower, 'advanced') !== false ||
                strpos($textLower, 'at10') !== false ||
                strpos($textLower, 'vertex 05') !== false ||
                strpos($textLower, 'neuron 02') !== false ||
                strpos($textLower, 'hack 04') !== false ||
                strpos($textLower, 'technical viper') !== false ||
                strpos($textLower, 'counter viper') !== false ||
                strpos($textLower, 'genius attack') !== false ||
                strpos($textLower, 'xplo') !== false ||
                strpos($textLower, 'top level') !== false) {
                return 'pro';
            }
            
            // Default to intermediate
            return 'intermediate';
        };

        // Parse and update the file
        $lines = explode("\n", $content);
        $updatedLines = [];
        $i = 0;
        $updatedCount = 0;

        while ($i < count($lines)) {
            $line = $lines[$i];
            
            // Check if this line contains 'name' key
            if (preg_match("/'name'\s*=>\s*'([^']+)'/", $line, $matches)) {
                $productName = $matches[1];
                $brand = $getBrand($productName);
                
                // Look ahead for description
                $description = '';
                $j = $i + 1;
                while ($j < count($lines) && $j < $i + 5) {
                    if (preg_match("/'description'\s*=>\s*'([^']+)'/", $lines[$j], $descMatches)) {
                        $description = $descMatches[1];
                        break;
                    }
                    $j++;
                }
                
                $level = $getLevel($productName, $description);
                
                // Add the current line
                $updatedLines[] = $line;
                
                // Find where to insert brand and level (after category line)
                $k = $i + 1;
                $inserted = false;
                while ($k < count($lines) && $k < $i + 15 && !$inserted) {
                    $updatedLines[] = $lines[$k];
                    
                    if (preg_match("/'category'\s*=>/", $lines[$k])) {
                        // Insert brand and level after category
                        $indent = str_repeat(' ', 16);
                        $brandValue = $brand ? "'$brand'" : 'null';
                        $levelValue = $level ? "'$level'" : 'null';
                        $updatedLines[] = $indent . "'brand'       => " . $brandValue . ",";
                        $updatedLines[] = $indent . "'level'       => " . $levelValue . ",";
                        $inserted = true;
                        $updatedCount++;
                    }
                    
                    $k++;
                }
                
                $i = $k;
            } else {
                $updatedLines[] = $line;
                $i++;
            }
        }

        // Write back to file
        File::put($seederFile, implode("\n", $updatedLines));

        $this->info("✅ Successfully updated $updatedCount products with brand and level data!");
        $this->info('Backup saved at: ' . $backupFile);
        
        return Command::SUCCESS;
    }
}
