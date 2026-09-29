<?php

namespace App\Http\Controllers;

use App\Models\Devotion;
use App\Models\Division;
use App\Models\Pages;
use App\Models\Pastor;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function show()
    {
        $homePage = Pages::where('name', 'Home')->with('sections')->first();

        $heroData = [];
        $visionMissionData = [];
        $carouselData = [];
        $scheduleData = [];
        $pastorGreetingData = [];

        if ($homePage) {
            foreach ($homePage->sections as $section) {
                switch ($section->name) {
                    case 'hero':
                        $heroData = $section->content;
                        break;
                    case 'vision_mission':
                        $visionMissionData = $section->content;
                        break;
                    case 'carousel':
                        $carouselData = $section->content;
                        break;
                    case 'schedule':
                        $scheduleData = $section->content;
                        break;
                    case 'pastor_greeting':
                        $pastorGreetingData = $section->content;
                        break;
                }
            }
        }
        
        return Inertia::render('Welcome', [
            'meta' => $homePage?->sections->firstWhere('name', 'meta')?->content ?? [],
            'hero' => $heroData,
            'visionMission' => $visionMissionData,
            'carousel' => $carouselData,
            'schedule' => $scheduleData,
            'pastorGreeting' => $pastorGreetingData,
            'divisions' => Division::ordered()->get(),
            'latestDevotions' => Devotion::latest()->take(3)->get()
                ->map(fn (Devotion $d) => $d->append(['snippet', 'reading_minutes'])->makeHidden('content'))
                ->values(),
        ]);
    }
}
