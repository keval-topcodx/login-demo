<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chat extends Model
{
    protected $fillable = ['user_id'];
    /**
     * @var mixed
     */

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessages::class);
    }

}
