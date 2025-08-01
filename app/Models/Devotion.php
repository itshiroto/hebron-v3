<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Devotion extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'category', 'image', 'content', 'published_at'];
}
