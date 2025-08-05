<?php

namespace App\Http\Controllers;

use App\Models\Pages;
use App\Models\Division;
use Inertia\Inertia;

class DivisionController extends Controller
{
    public function show()
    {
        $divisionsData = Division::all();

        $divisionPage = Pages::where('name', 'Division Page')->with('sections')->first();

        $heroData = [];

        if ($divisionPage) {
            foreach ($divisionPage->sections as $section) {
                switch ($section->name) {
                    case 'hero':
                        $heroData = $section->content;
                        break;
                }
            }
        }

        return Inertia::render('Division', [
            'data' => $divisionsData,
            'hero' => $heroData,
        ]);
    }
}
