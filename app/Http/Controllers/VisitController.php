<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Pages;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class VisitController extends Controller
{
    public function show()
    {
        $page = Pages::where('name', 'Visit')->with('sections')->first();
        $section = fn (string $name) => $page?->sections->firstWhere('name', $name)?->content ?? [];

        $hero = $section('hero');
        if (! empty($hero['image'])) {
            $hero['image'] = Storage::url($hero['image']);
        }

        $kids = $section('kids');
        $divisionIds = array_map('intval', $kids['division_ids'] ?? []);

        return Inertia::render('Visit', [
            'meta' => $section('meta'),
            'hero' => $hero,
            'expect' => $section('expect'),
            'arrival' => $section('arrival'),
            'kids' => $kids,
            'kidsDivisions' => Division::ordered()->whereIn('id', $divisionIds)->get(),
            'otherDivisions' => Division::ordered()->whereNotIn('id', $divisionIds)->get(),
            'welcome' => $section('welcome'),
            'faq' => $section('faq'),
        ]);
    }
}
