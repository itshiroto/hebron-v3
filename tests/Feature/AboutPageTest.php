<?php

use App\Models\Pages;
use Inertia\Testing\AssertableInertia as Assert;

it('passes the beliefs section to the about page', function () {
    $about = Pages::create(['name' => 'About']);
    $about->sections()->create(['name' => 'beliefs', 'content' => [
        'title' => 'Apa yang kami percaya',
        'intro' => '<p>Sebagai bagian dari GPdI…</p>',
        'link' => 'https://example.org/pengakuan-iman',
        'items' => [['title' => 'Alkitab', 'desc' => 'Firman Allah.', 'verse' => '2 Timotius 3:16']],
    ]]);

    $this->get('/about')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('AboutUs')
            ->where('beliefs.title', 'Apa yang kami percaya')
            ->where('beliefs.items.0.verse', '2 Timotius 3:16'));
});

it('sends empty beliefs when none are configured', function () {
    $this->get('/about')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('AboutUs')->where('beliefs', []));
});
