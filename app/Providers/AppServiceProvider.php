<?php

namespace App\Providers;

use Filament\Forms\Components\FileUpload;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Process;
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
        if (str_starts_with((string) $file->getMimeType(), 'video/')) {
            return self::storeVideo($component, $file);
        }

        // SVG and GIF (animation) keep Filament's default handling.
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

    /**
     * Background videos play muted, so drop audio and re-encode to H.264 MP4 (max 1920px wide).
     * Keeps the original when ffmpeg is missing, fails, or doesn't make the file smaller.
     */
    public static function storeVideo(FileUpload $component, TemporaryUploadedFile $file): ?string
    {
        $tmp = sys_get_temp_dir().'/'.Str::ulid().'.mp4';
        // ponytail: runs inside the save request (~10–30 s for a 12 MB clip, under nginx's 60 s); move to a queued job if uploads get bigger.
        try {
            $ok = Process::timeout(50)->run([
                'ffmpeg', '-y', '-i', $file->getRealPath(),
                '-an', '-vf', 'scale=trunc(min(1920\\,iw)/2)*2:-2', '-c:v', 'libx264', '-preset', 'veryfast', '-crf', '28',
                '-pix_fmt', 'yuv420p', '-movflags', '+faststart', $tmp,
            ])->successful();
        } catch (\Throwable) {
            $ok = false; // ffmpeg not installed or timed out
        }

        if (! $ok || ! is_file($tmp) || filesize($tmp) >= $file->getSize()) {
            @unlink($tmp);

            return $component->saveUploadedFile($file);
        }

        $path = $component->getDisk()->putFileAs($component->getDirectory() ?? '', new File($tmp), Str::ulid().'.mp4', $component->getVisibility());
        unlink($tmp);
        $file->delete();

        return $path ?: null;
    }
}
