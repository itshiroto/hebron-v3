<?php

namespace App\Providers;

use Filament\Forms\Components\FileUpload;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Image;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // Every admin image upload: shrink in the browser, then re-encode as WebP on the server.
        FileUpload::configureUsing(fn (FileUpload $upload) => $upload
            ->automaticallyResizeImagesMode('contain')
            ->automaticallyResizeImagesToWidth('1920')
            ->automaticallyResizeImagesToHeight('1920')
            ->automaticallyUpscaleImagesWhenResizing(false)
            ->saveUploadedFileUsing(fn (FileUpload $component, TemporaryUploadedFile $file) => self::storeUpload($component, $file)));
    }

    public static function storeUpload(FileUpload $component, TemporaryUploadedFile $file): ?string
    {
        // SVG, GIF (animation) and video keep Filament's default handling.
        if (! in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'])) {
            return $component->saveUploadedFile($file);
        }

        $tmp = sys_get_temp_dir().'/'.Str::ulid().'.webp';
        Image::load($file->getRealPath())->fit(Fit::Max, 1920, 1920)->quality(80)->save($tmp);

        $path = $component->getDisk()->putFileAs($component->getDirectory() ?? '', new File($tmp), Str::ulid().'.webp', $component->getVisibility());
        unlink($tmp);
        $file->delete();

        return $path ?: null;
    }
}
