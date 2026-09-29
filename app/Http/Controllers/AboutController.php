<?php

namespace App\Http\Controllers;

use App\Models\Pages;
use App\Models\Pastor;
use Inertia\Inertia;

class AboutController extends Controller
{
    public function show()
    {
        $aboutPage = Pages::where('name', 'About')->with('sections')->first();
        $pastorsData = Pastor::all();

        $heroData = [];
        $visionMissionData = [];
        $valuesData = [];
        $beliefsData = [];

        if ($aboutPage) {
            foreach ($aboutPage->sections as $section) {
                switch ($section->name) {
                    case 'hero':
                        $heroData = $section->content;
                        break;
                    case 'vision_mission':
                        $visionMissionData = $section->content;
                        break;
                    case 'values':
                        $valuesData = $section->content;
                        break;
                    case 'beliefs':
                        $beliefsData = $section->content;
                        break;
                }
            }
        }

        return Inertia::render('AboutUs', [
            'meta' => $aboutPage?->sections->firstWhere('name', 'meta')?->content ?? [],
            'hero' => $heroData,
            'visionMission' => $visionMissionData,
            'values' => $valuesData,
            'beliefs' => $beliefsData,
            'pastors' => $pastorsData
        ]);
    }
}
