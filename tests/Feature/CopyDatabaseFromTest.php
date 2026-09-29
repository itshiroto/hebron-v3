<?php

use App\Models\Division;
use App\Models\User;
use Illuminate\Support\Facades\DB;

it('replaces the default database with the rows from the source', function () {
    $file = tempnam(sys_get_temp_dir(), 'src');
    config(['database.connections.src' => ['driver' => 'sqlite', 'database' => $file, 'prefix' => '']]);
    $this->artisan('migrate', ['--database' => 'src'])->assertSuccessful();
    DB::connection('src')->table('users')->insert(['id' => 7, 'name' => 'Old', 'email' => 'old@example.com', 'password' => 'x']);
    DB::connection('src')->table('divisions')->insert(['id' => 3, 'title' => 'Youth', 'description' => 'd', 'image' => 'i.jpg']);

    Division::factory()->create(['title' => 'Stale']);

    $this->artisan('db:copy-from', ['url' => 'sqlite:///'.$file, '--force' => true])->assertSuccessful();

    expect(User::pluck('email', 'id')->all())->toBe([7 => 'old@example.com'])
        ->and(Division::pluck('title', 'id')->all())->toBe([3 => 'Youth']);

    unlink($file);
});
