<?php

namespace App\Services;

use App\Models\DemoContent;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class DemoCleanupService
{
    /**
     * Clean expired demo content older than given minutes (default: 3 minutes)
     */
    public static function cleanExpired(int $minutes = 3): int
    {
        try {
            if (!Schema::hasTable('demo_contents')) {
                return 0;
            }

            $threshold = now()->subMinutes($minutes);
            $expiredItems = DemoContent::where('created_at', '<=', $threshold)->get();

            if ($expiredItems->isEmpty()) {
                return 0;
            }

            $deletedCount = 0;

            foreach ($expiredItems as $item) {
                try {
                    $modelClass = $item->content_type;
                    if (class_exists($modelClass)) {
                        $record = $modelClass::find($item->content_id);
                        if ($record) {
                            // If model has image file fields, clean them if needed
                            if (isset($record->image) && $record->image && Storage::disk('public')->exists($record->image)) {
                                Storage::disk('public')->delete($record->image);
                            }
                            if (isset($record->avatar) && $record->avatar && Storage::disk('public')->exists($record->avatar)) {
                                Storage::disk('public')->delete($record->avatar);
                            }

                            if (method_exists($record, 'forceDelete')) {
                                $record->forceDelete();
                            } else {
                                $record->delete();
                            }
                            $deletedCount++;
                        }
                    }
                } catch (\Throwable $e) {
                    // Continue to next item
                } finally {
                    $item->delete();
                }
            }

            return $deletedCount;
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
