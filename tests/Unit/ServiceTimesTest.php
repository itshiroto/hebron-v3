<?php

use App\Support\ServiceTimes;

it('strips a trailing WIB and defaults livestream to false', function () {
    expect(ServiceTimes::normalize([
        ['title' => 'Ibadah Raya 1', 'time' => '07.30 WIB'],
        ['title' => 'Ibadah Raya 2', 'time' => '10.00', 'livestream' => true],
        ['title' => 'Ibadah Raya 3', 'time' => ' 17.30 wib '],
    ]))->toBe([
        ['title' => 'Ibadah Raya 1', 'time' => '07.30', 'livestream' => false],
        ['title' => 'Ibadah Raya 2', 'time' => '10.00', 'livestream' => true],
        ['title' => 'Ibadah Raya 3', 'time' => '17.30', 'livestream' => false],
    ]);
});

it('returns an empty list for missing or malformed input', function () {
    expect(ServiceTimes::normalize(null))->toBe([])
        ->and(ServiceTimes::normalize('nope'))->toBe([])
        ->and(ServiceTimes::normalize([['title' => 'No time']]))->toBe([]);
});
