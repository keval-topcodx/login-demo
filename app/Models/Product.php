<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'title',
        'description',
        'status',
        'images',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'images' => 'array',
    ];
    protected $appends = ['image_urls'];

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('status', 1);
    }

    public function getImageUrlsAttribute(): array
    {
        $urls = [];

        $mediaItems = $this->getMedia('products');

        foreach ($mediaItems as $media) {
            $urls[] = $media->getUrl();
        }

        return $urls;
    }
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->chaperone();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)
            ->withPivot('created_at', 'updated_at');
    }


}
