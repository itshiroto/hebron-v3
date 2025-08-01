<?php

namespace App\Http\Controllers;

use App\Models\Devotion;
use Inertia\Inertia;
use Illuminate\Support\Str;

class DevotionController extends Controller
{
    public function all() {
        $data = Devotion::all()->map(function ($devotion) {
            $plainContent = strip_tags($devotion->content);
            $devotion->snippet = Str::words($plainContent, 50); // Limit to 50 words

            return $devotion;
        });

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
