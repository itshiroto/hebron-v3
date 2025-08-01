<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sections extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'content', 'page_id'];

    protected $casts = [
        'content' => 'array',
    ];

    public function page()
    {
        return $this->belongsTo(Pages::class);
    }
}
