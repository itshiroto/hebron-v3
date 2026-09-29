<?php

namespace App\Http\Middleware;

use App\Models\Pages;
use App\Support\ServiceTimes;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // Fetch general settings
        $settingsPage = Pages::firstOrCreate(['name' => 'Settings']);
        $generalSettingsSection = $settingsPage->sections()->firstOrCreate(
            ['name' => 'general_settings', 'page_id' => $settingsPage->id],
            ['content' => []]
        );
        $generalSettings = $generalSettingsSection->content;

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'generalSettings' => $generalSettings, // Share general settings
            'services' => fn () => ServiceTimes::normalize(
                Pages::where('name', 'Home')->first()
                    ?->sections()->where('name', 'schedule')->first()
                    ?->content['services'] ?? null
            ),
        ];
    }
}
