<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Pages;
use App\Models\Sections;

class MinistryController extends Controller
{
    public function show() {
        $ministryPage = Pages::where('name', 'Ministries Page')->first();
        $sectionsData = [];

        if ($ministryPage) {
            $sections = $ministryPage->sections()->get();
            foreach ($sections as $section) {
                $sectionsData[$section->name] = $section->content;
            }
        }

        return Inertia::render('Ministry', [
            'meta' => $sectionsData['meta'] ?? [],
            'sections' => $sectionsData,
        ]);
    }
}
