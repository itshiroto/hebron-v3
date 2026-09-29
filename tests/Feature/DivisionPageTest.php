<?php

use App\Models\Division;
use Inertia\Testing\AssertableInertia as Assert;

it('lists divisions in sort order with a url slug', function () {
    Division::factory()->create(['title' => 'Speaker of Truth', 'sort' => 2, 'short_time' => 'Sab 16.30', 'audience' => 'Mahasiswa']);
    Division::factory()->create(['title' => "DoT's", 'sort' => 1, 'time' => null, 'short_time' => null, 'audience' => null]);

    $this->get('/division')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Division')
            ->has('data', 2)
            ->where('data.0.title', "DoT's")
            ->where('data.0.slug', 'dots')
            ->where('data.0.time', null)
            ->where('data.0.short_time', null)
            ->where('data.1.slug', 'speaker-of-truth')
            ->where('data.1.short_time', 'Sab 16.30')
            ->where('data.1.audience', 'Mahasiswa'));
});

it('renders the divisions page with no divisions', function () {
    $this->get('/division')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Division')->has('data', 0));
});
