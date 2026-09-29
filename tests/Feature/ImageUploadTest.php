<?php

use App\Filament\Pages\HomePage;
use App\Filament\Resources\PastorResource\Pages\CreatePastor;
use App\Models\Pages;
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

it('compresses the home hero video to a silent mp4', function () {
    $src = sys_get_temp_dir().'/hero-src.mp4';
    exec('ffmpeg -y -loglevel error -f lavfi -i testsrc2=size=2560x1440:rate=30:duration=2 -f lavfi -i sine=duration=2 -c:v libx264 -crf 10 -c:a aac -shortest '.escapeshellarg($src), result_code: $code);
    if ($code !== 0) {
        $this->markTestSkipped('ffmpeg not available');
    }

    $disk = Storage::fake(config('filament.default_filesystem_disk'));
    $this->actingAs(User::factory()->create(['email' => 'admin@gpdi-hebron.com']));

    Livewire::test(HomePage::class)
        ->fillForm([
            'heroData.image' => UploadedFile::fake()->image('hero.jpg'),
            'heroData.video' => UploadedFile::fake()->createWithContent('hero.mp4', file_get_contents($src))->mimeType('video/mp4'),
            'visionMissionData' => ['visi' => ['title' => 'Visi', 'desc' => '<p>v</p>'], 'misi' => ['title' => 'Misi', 'desc' => '<p>m</p>']],
            'pastorGreetingData' => ['pastors_name' => 'Pdt. A', 'desc' => '<p>d</p>', 'pastor_img' => UploadedFile::fake()->image('p.jpg')],
        ])
        ->call('submit')
        ->assertHasNoFormErrors();

    $path = Pages::firstWhere('name', 'Home')->sections()->firstWhere('name', 'hero')->content['video'];
    expect($path)->toStartWith('home-page-hero/')->toEndWith('.mp4')
        ->and($disk->size($path))->toBeLessThan(filesize($src));

    $probe = shell_exec('ffprobe -v error -show_entries stream=codec_type,width -of csv=p=0 '.escapeshellarg($disk->path($path)));
    expect(trim($probe))->toBe('video,1920');
});
