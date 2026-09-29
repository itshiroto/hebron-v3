<?php

use App\Models\Devotion;
use App\Models\Division;
use App\Models\Pages;
use Inertia\Testing\AssertableInertia as Assert;

it('passes ordered divisions, the 3 newest devotions and normalised services', function () {
    Division::factory()->create(['title' => 'B', 'sort' => 2]);
    Division::factory()->create(['title' => 'A', 'sort' => 1]);
    foreach (range(1, 4) as $i) {
        Devotion::factory()->create(['title' => "Post $i", 'created_at' => now()->subDays(10 - $i)]);
    }
    $home = Pages::create(['name' => 'Home']);
    $home->sections()->create(['name' => 'schedule', 'content' => [
        'services' => [['title' => 'Ibadah Raya 2', 'time' => '10.00 WIB', 'livestream' => true]],
    ]]);

    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->where('divisions.0.title', 'A')
            ->where('divisions.1.title', 'B')
            ->has('latestDevotions', 3)
            ->where('latestDevotions.0.title', 'Post 4')
            ->has('latestDevotions.0.snippet')
            ->has('latestDevotions.0.reading_minutes')
            ->missing('latestDevotions.0.content')
            ->where('services.0.time', '10.00')
            ->where('services.0.livestream', true));
});

it('renders the homepage on an empty database', function () {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->has('divisions', 0)
            ->has('latestDevotions', 0)
            ->has('services', 0));
});
