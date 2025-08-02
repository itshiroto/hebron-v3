<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class MinistryController extends Controller
{
    public function show() {
        return Inertia::render('Ministry');
    }
}
