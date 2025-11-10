<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chat extends Model
{
    protected $fillable = ['user_id', 'archived'];
    /**
     * @var mixed
     */

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessages::class);
    }

    #[Scope]
    protected function archived(Builder $query): void
    {
        $query->where('archived', 1);
    }

}
