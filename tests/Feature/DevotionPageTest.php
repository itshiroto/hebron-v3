<?php

use App\Models\Devotion;
use Inertia\Testing\AssertableInertia as Assert;

it('paginates devotions 9 per page, newest first, without content', function () {
    foreach (range(1, 10) as $i) {
        Devotion::factory()->create(['title' => "Post $i", 'created_at' => now()->subDays(20 - $i)]);
    }

    $this->get('/devotion')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('DevotionList')
            ->where('category', null)
            ->has('posts.data', 9)
            ->where('posts.data.0.title', 'Post 10')
            ->has('posts.data.0.snippet')
            ->missing('posts.data.0.content')
            ->where('posts.last_page', 2));
});

it('filters by a known category and keeps the filter in pagination links', function () {
    Devotion::factory()->count(2)->create(['category' => 'news']);
    Devotion::factory()->count(3)->create(['category' => 'devotion']);

    $this->get('/devotion?category=news')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('category', 'news')
            ->has('posts.data', 2)
            ->where('posts.data.0.category', 'news'));
});

it('ignores unknown or malformed categories and out-of-range pages', function () {
    Devotion::factory()->count(2)->create();

    $this->get('/devotion?category=foo')->assertOk()
        ->assertInertia(fn (Assert $page) => $page->where('category', null)->has('posts.data', 2));
    $this->get('/devotion?category[]=news')->assertOk()
        ->assertInertia(fn (Assert $page) => $page->where('category', null)->has('posts.data', 2));
    $this->get('/devotion?page=999')->assertOk()
        ->assertInertia(fn (Assert $page) => $page->has('posts.data', 0));
});

it('shows a post with up to 3 related posts excluding itself', function () {
    $post = Devotion::factory()->create(['slug' => 'kasih-karunia', 'created_at' => now()]);
    Devotion::factory()->count(4)->create(['created_at' => now()->subDay()]);

    $this->get('/devotion/kasih-karunia')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Devotion')
            ->where('data.slug', 'kasih-karunia')
            ->has('data.content')
            ->has('data.reading_minutes')
            ->has('related', 3)
            ->missing('related.0.content')
            ->where('related', fn ($related) => collect($related)->pluck('id')->doesntContain($post->id)));
});

it('returns 404 for an unknown slug', function () {
    $this->get('/devotion/tidak-ada')->assertNotFound();
});
