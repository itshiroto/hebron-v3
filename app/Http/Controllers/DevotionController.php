<?php

namespace App\Http\Controllers;

use App\Models\Devotion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DevotionController extends Controller
{
    public function single(Request $request, string $slug)
    {
        $data = Devotion::where('slug', $slug)->first();
        // TODO: bikin jadi published_at
        $data["updated_at"] = $data["updated_at"]->format('d/m/Y');
        return Inertia::render('Devotion', ['data' => $data]);
    }
}
