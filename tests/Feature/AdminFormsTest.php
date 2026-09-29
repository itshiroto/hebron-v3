<?php

use App\Models\Division;
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
