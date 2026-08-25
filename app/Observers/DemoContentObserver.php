<?php

namespace App\Observers;

use App\Models\DemoContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DemoContentObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        // Don't track DemoContent itself
        if ($model instanceof DemoContent) {
            return;
        }

        // Check if currently authenticated user is a demo user
        $user = Auth::user();
        if ($user && method_exists($user, 'isDemo') && $user->isDemo()) {
            try {
                DemoContent::create([
                    'user_id' => $user->id,
                    'content_type' => get_class($model),
                    'content_id' => $model->getKey(),
                ]);
            } catch (\Throwable $e) {
                // Silently handle if table not yet migrated
            }
        }
    }
}
