<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Division extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'audience', 'description', 'image', 'time', 'short_time', 'link', 'sort'];

    protected $appends = ['slug'];

    protected $casts = ['sort' => 'integer'];

    public function getSlugAttribute(): string
    {
        return Str::slug($this->title);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderBy('id');
    }
}
