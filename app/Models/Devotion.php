<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Devotion extends Model
{
    use HasFactory;

    public const CATEGORIES = ['devotion', 'news', 'announcement'];

    protected $fillable = ['title', 'slug', 'category', 'image', 'content', 'published_at'];

    public function getSnippetAttribute(): string
    {
        return Str::words(trim(strip_tags((string) $this->content)), 50);
    }

    public function getReadingMinutesAttribute(): int
    {
        $words = preg_split('/\s+/', trim(strip_tags((string) $this->content)), -1, PREG_SPLIT_NO_EMPTY);

        return max(1, (int) ceil(count($words) / 200));
    }
}
