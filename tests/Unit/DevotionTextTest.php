<?php

use App\Models\Devotion;

it('keeps words separated across html tags in the snippet', function () {
    $devotion = new Devotion(['content' => '<p>Paragraf satu.</p><p>Paragraf dua.</p><blockquote><p>Ayat</p><cite>Mazmur 133:1</cite></blockquote><h2>Subjudul</h2>']);

    expect($devotion->snippet)->toBe('Paragraf satu. Paragraf dua. Ayat Mazmur 133:1 Subjudul');
});

it('counts words across html tags for the reading time', function () {
    $devotion = new Devotion(['content' => str_repeat('<p>kata</p>', 201)]);

    expect($devotion->reading_minutes)->toBe(2);
});
