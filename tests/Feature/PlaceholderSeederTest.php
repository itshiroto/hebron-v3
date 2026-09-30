<?php

use App\Models\Devotion;
use App\Models\Pages;
use Database\Seeders\PlaceholderSeeder;
use Illuminate\Support\Facades\Storage;

it('fills every page with placeholder content and images that exist', function () {
    Storage::fake('public');

    $this->seed(PlaceholderSeeder::class);

    // Every image path stored in page content points at a generated file.
    $paths = [];
    foreach (Pages::with('sections')->get() as $page) {
        $contents = $page->sections->pluck('content')->all();
        array_walk_recursive($contents, function ($value) use (&$paths) {
            if (is_string($value) && preg_match('/\.(jpg|png)$/', $value)) {
                $paths[] = $value;
            }
        });
    }
    expect($paths)->not->toBeEmpty();
    foreach ($paths as $path) {
        Storage::disk('public')->assertExists($path);
    }
    [$width, $height] = getimagesizefromstring(Storage::disk('public')->get('home-page-hero/placeholder-home-hero.jpg'));
    expect([$width, $height])->toBe([1920, 1080]);

    foreach (['/', '/about', '/division', '/ministry', '/devotion', '/contact', '/visit', '/devotion/'.Devotion::first()->slug] as $url) {
        $this->get($url)->assertOk();
    }
});

it('updates instead of duplicating when run twice', function () {
    Storage::fake('public');

    $this->seed(PlaceholderSeeder::class);
    $this->seed(PlaceholderSeeder::class);

    expect(\App\Models\Division::count())->toBe(7)
        ->and(\App\Models\Pastor::count())->toBe(4)
        ->and(Devotion::count())->toBe(6);
});
