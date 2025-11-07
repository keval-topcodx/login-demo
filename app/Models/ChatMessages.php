<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessages extends Model
{
    protected $fillable = [
        'chat_id',
        'user_type',
        'message',
        'attachment_name',
        'attachment_url',
        'created_at',
        'updated_at',
    ];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

}
