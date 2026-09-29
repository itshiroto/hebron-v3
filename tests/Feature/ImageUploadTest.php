<?php

use App\Filament\Resources\PastorResource\Pages\CreatePastor;
use App\Models\Pastor;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

it('stores admin image uploads as downscaled webp', function () {
    $disk = Storage::fake(config('filament.default_filesystem_disk'));
    $this->actingAs(User::factory()->create(['email' => 'admin@gpdi-hebron.com']));

    Livewire::test(CreatePastor::class)
        ->fillForm(['name' => 'Pdt. A', 'position' => 'Gembala', 'image' => UploadedFile::fake()->image('big.jpg', 4000, 2000)])
        ->call('create')
        ->assertHasNoFormErrors();

    $path = Pastor::first()->image;
    expect($path)->toStartWith('pastors-image/')->toEndWith('.webp');
    [$w, $h] = getimagesizefromstring($disk->get($path));
    expect([$w, $h])->toBe([1920, 960]);
});
