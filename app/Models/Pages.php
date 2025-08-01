<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pages extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function sections()
    {
        return $this->hasMany(Sections::class, 'page_id');
    }
}
