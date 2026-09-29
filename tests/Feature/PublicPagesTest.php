<?php

it('renders every public page on an empty database', function (string $url) {
    $this->get($url)->assertOk();
})->with(['/', '/about', '/division', '/ministry', '/devotion', '/contact']);
