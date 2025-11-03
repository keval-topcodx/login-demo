<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


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

    public function getImageUrlsAttribute(): array
    {
        $urls = [];

        $mediaItems = $this->getMedia('products');

        foreach ($mediaItems as $media) {
            $urls[] = $media->getUrl();
        }

        return $urls;
    }

//    protected function imageUrls(): Attribute
//    {
//        $imageUrlsArray = [];
//        foreach ($this->images as $image) {
//            $path = Storage::url($image);
//            $imageUrlsArray[] = $path;
//        }
//        return Attribute::make(
//            get: fn($value) => $imageUrlsArray,
//        );
//    }
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
