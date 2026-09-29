<?php

it('renders every public page on an empty database', function (string $url) {
    $this->get($url)->assertOk();
})->with(['/', '/about', '/division', '/ministry', '/devotion', '/contact']);

it('renders CMS SEO meta tags server-side', function () {
    \App\Models\Pages::create(['name' => 'About'])->sections()->create(['name' => 'meta', 'content' => [
        'title' => 'Tentang Hebron',
        'description' => 'Gereja keluarga di Gading Serpong.',
        'image' => 'seo/about.jpg',
    ]]);

    $this->get('/about')
        ->assertOk()
        ->assertSee('<title inertia>Tentang Hebron - '.config('app.name').'</title>', false)
        ->assertSee('<meta name="description" content="Gereja keluarga di Gading Serpong.">', false)
        ->assertSee('/storage/seo/about.jpg', false);
});
