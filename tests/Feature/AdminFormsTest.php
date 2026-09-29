<?php

use App\Models\Division;
use App\Models\Pages;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create(['email' => 'admin@gpdi-hebron.com']));
});

it('shows the new division fields in the admin form', function () {
    $this->get('/admin/divisions/divisions/create')
        ->assertOk()
        ->assertSee('data.audience', false)
        ->assertSee('data.short_time', false);
});

it('lists divisions in the admin table', function () {
    Division::factory()->create(['title' => 'Seeds of Truth']);

    $this->get('/admin/divisions/divisions')
        ->assertOk()
        ->assertSee('Seeds of Truth');
});

it('shows the hero video and livestream fields on the home admin page', function () {
    Pages::create(['name' => 'Home'])->sections()->create(['name' => 'schedule', 'content' => [
        'services' => [['title' => 'Ibadah Raya 2', 'time' => '10.00']],
    ]]);

    $this->get('/admin/home-page')
        ->assertOk()
        ->assertSee('heroData.video', false)
        ->assertSee('Maks. 12 MB')
        ->assertSee('Live Streaming');
});

it('shows the beliefs section on the about admin page', function () {
    $this->get('/admin/about-page')
        ->assertOk()
        ->assertSee('beliefsData.title', false)
        ->assertSee('Pengakuan Iman');
});

it('shows the maps and livestream links on the general settings page', function () {
    $this->get('/admin/general-settings')
        ->assertOk()
        ->assertSee('generalSettingsData.maps_url', false)
        ->assertSee('generalSettingsData.livestream_url', false);
});

it('shows the SEO section on every page editor', function (string $url) {
    $this->get($url)
        ->assertOk()
        ->assertSee('metaData.title', false)
        ->assertSee('metaData.description', false);
})->with(['/admin/home-page', '/admin/about-page', '/admin/contact-page', '/admin/ministry-page', '/admin/divisions/division-page']);
