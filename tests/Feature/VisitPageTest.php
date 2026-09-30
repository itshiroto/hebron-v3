<?php

use App\Models\Division;
use App\Models\Pages;
use Inertia\Testing\AssertableInertia as Assert;

it('shows only the divisions picked in the CMS, in sort order', function () {
    $kids = Division::factory()->create(['title' => 'Seeds of Truth', 'sort' => 0]);
    $youth = Division::factory()->create(['title' => 'Descendants of Truth', 'sort' => 1]);
    Division::factory()->create(['title' => 'Men of God', 'sort' => 2]);

    Pages::create(['name' => 'Visit'])->sections()->create(['name' => 'kids', 'content' => [
        'title' => 'Bawa anak',
        'division_ids' => [(string) $youth->id, (string) $kids->id],
    ]]);

    $this->get('/visit')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Visit')
            ->has('kidsDivisions', 2)
            ->where('kidsDivisions.0.title', 'Seeds of Truth')
            ->where('kidsDivisions.1.title', 'Descendants of Truth')
            ->where('kids.title', 'Bawa anak'));
});

it('passes office hours and request types to the contact page', function () {
    $contact = Pages::create(['name' => 'Contact']);
    $contact->sections()->create(['name' => 'office_hours', 'content' => ['hours' => '08.00 WIB']]);
    $contact->sections()->create(['name' => 'requests', 'content' => ['whatsapp' => '6281', 'items' => [['title' => 'Doa']]]]);

    $this->get('/contact')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Contact')
            ->where('officeHours.hours', '08.00 WIB')
            ->where('requests.items.0.title', 'Doa'));
});
