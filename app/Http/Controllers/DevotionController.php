<?php

namespace App\Http\Controllers;

use App\Models\Devotion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DevotionController extends Controller
{
    public function all() {
        $data = Devotion::all();
        return Inertia::render('DevotionList', ['data' => $data]);
    }
    public function single(string $slug)
    {
        $data = Devotion::where('slug', $slug)->first();

        if (!$data) {
            abort(404);
        }

        return Inertia::render('Devotion', ['data' => $data]);
    }
}
