<?php

namespace App\Http\Controllers;

use App\Models\Devotion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DevotionController extends Controller
{
    public function all(Request $request)
    {
        $category = $request->query('category');
        if (! is_string($category) || ! in_array($category, Devotion::CATEGORIES, true)) {
            $category = null;
        }

        $posts = Devotion::query()
            ->when($category, fn ($query) => $query->where('category', $category))
            ->latest()
            ->paginate(9)
            ->withQueryString()
            ->through(fn (Devotion $d) => $d->append(['snippet', 'reading_minutes'])->makeHidden('content'));

        return Inertia::render('DevotionList', [
            'posts' => $posts,
            'category' => $category,
        ]);
    }

    public function single(string $slug)
    {
        $data = Devotion::where('slug', $slug)->firstOrFail();

        $related = Devotion::whereKeyNot($data->getKey())
            ->latest()
            ->take(3)
            ->get()
            ->map(fn (Devotion $d) => $d->append('reading_minutes')->makeHidden('content'))
            ->values();

        return Inertia::render('Devotion', [
            'data' => $data->append('reading_minutes'),
            'related' => $related,
        ]);
    }
}
