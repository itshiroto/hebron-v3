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
        return Str::words($this->plainText(), 50);
    }

    public function getReadingMinutesAttribute(): int
    {
        $words = preg_split('/\s+/', $this->plainText(), -1, PREG_SPLIT_NO_EMPTY);

        return max(1, (int) ceil(count($words) / 200));
    }

    /** Content as plain text, with tag boundaries turned into spaces so words don't run together. */
    private function plainText(): string
    {
        $text = strip_tags(preg_replace('/<[^>]*>/', ' $0 ', (string) $this->content));

        return trim(preg_replace('/\s+/', ' ', html_entity_decode($text)));
    }
}
