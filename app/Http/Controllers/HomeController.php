<?php

namespace App\Http\Controllers;

use App\Models\Pages;
use App\Models\Pastor;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function show()
    {
        $homePage = Pages::where('name', 'Home')->with('sections')->first();
        dd($homePage);
        $heroData = [];
        $visionMissionData = [];
        $ministriesData = [];

        if ($homePage) {
            foreach ($homePage->sections as $section) {
                switch ($section->name) {
                    case 'hero':
                        $heroData = $section->content;
                        break;
                    case 'vision_mission':
                        $visionMissionData = $section->content;
                        break;
                    case 'ministries':
                        $ministriesData = $section->content;
                        break;
                }
            }
        }

        return Inertia::render('AboutUs', [
            'hero' => $heroData,
            'visionMission' => $visionMissionData,
            'ministries' => $ministriesData,
        ]);
    }
}