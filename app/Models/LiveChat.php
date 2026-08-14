<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'status',
        'ip_address',
        'user_agent',
    ];

    public function messages()
    {
        return $this->hasMany(LiveChatMessage::class);
    }
}
