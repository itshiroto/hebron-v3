<?php

namespace App\Http\Controllers;

use App\Models\Devotion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str; // Import the Str facade

class DevotionController extends Controller
{
    public function all() {
        $data = Devotion::all()->map(function ($devotion) {
            // Strip HTML tags and create a snippet
            $plainContent = strip_tags($devotion->content);
            $devotion->snippet = Str::limit($plainContent, 150); // Limit to 150 characters

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
