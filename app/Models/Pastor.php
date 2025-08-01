<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pastor extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'position', 'image'];
}
