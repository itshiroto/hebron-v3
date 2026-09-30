<?php

namespace Database\Seeders;

use App\Models\Devotion;
use App\Models\Division;
use App\Models\Pages;
use App\Models\Pastor;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Fills every CMS field with placeholder text and generated images, so a fresh
 * install renders every section without production content or storage files.
 * Overwrites page content; divisions, pastors and devotions are keyed by their
 * placeholder titles, so re-running updates them instead of duplicating.
 *
 * php artisan db:seed --class=PlaceholderSeeder
 */
class PlaceholderSeeder extends Seeder
{
    private const COLORS = ['#173f55', '#27617f', '#1f5046', '#6b4f2a', '#4a3f6b'];

    private Generator $faker;

    public function run(): void
    {
        $this->faker = FakerFactory::create('id_ID');
        $this->faker->seed(2026);

        foreach ($this->pages() as $pageName => $sections) {
            $page = Pages::firstOrCreate(['name' => $pageName]);
            foreach ($sections as $name => $content) {
                $page->sections()->updateOrCreate(['name' => $name], ['content' => $content]);
            }
        }

        foreach (['Anak', 'Remaja', 'Mahasiswa', 'Profesional Muda', 'Keluarga Muda', 'Kaum Pria', 'Kaum Wanita'] as $sort => $audience) {
            $title = "Divisi {$audience}";
            $day = $sort % 2 ? 'Sabtu' : 'Minggu';
            $time = sprintf('%02d.00', 10 + $sort);
            Division::updateOrCreate(['title' => $title], [
                'audience' => $audience,
                'description' => $this->faker->paragraph(3),
                'image' => $this->image('divisions-image', $title, 800, 1000),
                'time' => "Setiap {$day} · {$time} WIB",
                'short_time' => Str::substr($day, 0, 3)." {$time}",
                'link' => 'https://example.com/divisi/'.Str::slug($audience),
                'sort' => $sort,
            ]);
        }

        Pages::firstWhere('name', 'Visit')->sections()->updateOrCreate(['name' => 'kids'], ['content' => [
            'title' => $this->title(),
            'desc' => $this->faker->paragraph(3),
            'division_ids' => Division::whereIn('title', ['Divisi Anak', 'Divisi Remaja'])->pluck('id')->all(),
        ]]);

        foreach (range(1, 4) as $i) {
            Pastor::updateOrCreate(['name' => "Pdt. Nama Gembala {$i}"], [
                'position' => $i === 1 ? 'Gembala Sidang' : 'Gembala '.$this->faker->word(),
                'image' => $this->image('pastors-image', "Gembala {$i}", 800, 1000),
            ]);
        }

        foreach (['devotion', 'news', 'announcement', 'devotion', 'news', 'announcement'] as $i => $category) {
            $title = 'Judul Placeholder '.($i + 1).': '.$this->title(4);
            Devotion::updateOrCreate(['slug' => 'placeholder-'.($i + 1)], [
                'title' => $title,
                'category' => $category,
                'image' => $this->image('devotion-image', 'Renungan '.($i + 1), 1600, 900),
                'content' => $this->article(),
                'published_at' => now()->subWeeks($i)->toDateString(),
            ]);
        }
    }

    private function pages(): array
    {
        return [
            'Settings' => [
                'general_settings' => [
                    'church_name' => 'Nama Gereja',
                    'church_logo' => $this->image('site-settings', 'Logo', 240, 240, transparent: true),
                    'church_address' => '<p>'.$this->faker->address().'</p>',
                    'maps_url' => 'https://maps.google.com/?q=Gading+Serpong',
                    'livestream_url' => 'https://example.com/live',
                    'social_media' => [
                        ['platform' => 'Instagram', 'url' => 'https://www.instagram.com/example'],
                        ['platform' => 'YouTube', 'url' => 'https://www.youtube.com/@example'],
                        ['platform' => 'Facebook', 'url' => 'https://www.facebook.com/example'],
                    ],
                ],
            ],
            'Home' => [
                'meta' => $this->meta('Beranda'),
                'hero' => [
                    'title' => $this->title(7),
                    'image' => $this->image('home-page-hero', 'Home · Hero', 1920, 1080),
                    'video' => null,
                    'primary_button' => ['text' => 'Tombol Utama', 'link' => '/visit'],
                    'secondary_button' => ['text' => 'Tombol Kedua', 'link' => '/about'],
                ],
                'vision_mission' => [
                    'title' => 'Visi & Misi',
                    'visi' => ['title' => 'Visi', 'desc' => '<p>'.$this->faker->sentence(18).'</p>'],
                    'misi' => ['title' => 'Misi', 'desc' => $this->list()],
                    'about_button_text' => 'Tombol tentang kami',
                ],
                // 1–4: gallery, 5–7: Pelayanan, Renungan and Hubungi Kami cards.
                'carousel' => array_map(
                    fn (int $i) => $this->image('home-page-carousel', "Galeri {$i}", 1200, 1500),
                    range(1, 7),
                ),
                'schedule' => [
                    'title' => 'Ibadah Raya',
                    'services' => [
                        ['title' => 'Ibadah Raya 1', 'time' => '07.30', 'livestream' => false, 'note' => $this->faker->sentence(8)],
                        ['title' => 'Ibadah Raya 2', 'time' => '10.00', 'livestream' => true, 'note' => $this->faker->sentence(8)],
                        ['title' => 'Ibadah Raya 3', 'time' => '17.30', 'livestream' => false, 'note' => $this->faker->sentence(8)],
                    ],
                    'division_button_text' => 'Tombol semua divisi',
                ],
                'pastor_greeting' => [
                    'subtitle' => $this->title(4),
                    'pastors_name' => 'Pdt. Nama Gembala 1',
                    'pastors_position' => 'Gembala Sidang',
                    'pastor_img' => $this->image('home-page-pastor', 'Foto Gembala', 1000, 1300),
                    'pastor_signature' => $this->image('home-page-pastor', 'Tanda Tangan', 600, 200, transparent: true),
                    'desc' => '<p>"'.$this->faker->sentence(20).'"</p>',
                    'pastoral_button_text' => 'Tombol tim pastoral',
                ],
            ],
            'About' => [
                'meta' => $this->meta('Tentang Kami'),
                'hero' => [
                    'title' => $this->title(6),
                    'subtitle' => $this->faker->sentence(12),
                    'image' => $this->image('about-page-hero', 'Tentang · Hero', 1920, 1080),
                ],
                'vision_mission' => [
                    'visi' => ['title' => 'Visi', 'desc' => '<p>'.$this->faker->sentence(18).'</p>'],
                    'misi' => ['title' => 'Misi', 'desc' => $this->list()],
                    'image' => $this->image('about-page-vision-mission', 'Visi & Misi', 1200, 1500),
                ],
                'values' => array_map(fn (string $icon) => [
                    'icon' => $icon,
                    'title' => $this->title(1),
                    'desc' => '<p><strong>Kitab 1:1</strong></p><p>'.implode('<br>', $this->faker->sentences(3)).'</p>',
                ], ['lucide:flame', 'lucide:heart-handshake', 'lucide:hand-heart', 'lucide:globe']),
                'beliefs' => [
                    'title' => $this->title(4),
                    'intro' => '<p>'.$this->faker->paragraph(3).'</p>',
                    'link' => 'https://example.com/pengakuan-iman',
                    'items' => array_map(fn () => [
                        'title' => $this->title(2),
                        'desc' => $this->faker->sentence(16),
                        'verse' => 'Kitab '.$this->faker->numberBetween(1, 30).':'.$this->faker->numberBetween(1, 20),
                    ], range(1, 8)),
                ],
            ],
            'Ministries Page' => [
                'meta' => $this->meta('Pelayanan'),
                'hero' => [
                    'title' => $this->title(5),
                    'image' => $this->image('ministry-page-hero', 'Pelayanan · Hero', 1920, 1080),
                ],
                'intro' => [
                    'title' => $this->title(4),
                    'desc' => '<p>'.$this->faker->paragraph(5).'</p>',
                ],
                'serve' => [
                    'title' => $this->title(3),
                    'ministryList' => array_map(fn (int $i) => [
                        'title' => "Pelayanan {$i}",
                        'desc' => '<p>- '.implode('<br>- ', $this->faker->words(4)).'</p>',
                        'image' => $this->image('ministry-page-ministries', "Pelayanan {$i}", 1200, 900),
                        'link' => 'https://example.com/daftar-pelayanan',
                    ], range(1, 5)),
                ],
                'faq' => $this->faq(),
            ],
            'Division Page' => [
                'meta' => $this->meta('Divisi'),
                'hero' => [
                    'title' => $this->title(5),
                    'image' => $this->image('division-page-hero', 'Divisi · Hero', 1920, 1080),
                ],
            ],
            'Contact' => [
                'meta' => $this->meta('Kontak'),
                'hero' => [
                    'title' => $this->title(3),
                    'subtitle' => $this->faker->sentence(12),
                    'image' => $this->image('contact-page-hero', 'Kontak · Hero', 1920, 1080),
                ],
                'contact_list' => [
                    ['title' => 'Sekretariat', 'desc' => '0812 0000 0000', 'link' => null],
                    ['title' => 'Email', 'desc' => 'halo@example.com', 'link' => 'mailto:halo@example.com'],
                    ['title' => 'Instagram', 'desc' => '@example', 'link' => 'https://www.instagram.com/example'],
                ],
                'office_hours' => ['hours' => 'Senin – Jumat · 08.00 – 16.00 WIB'],
                'requests' => [
                    'title' => $this->title(3),
                    'whatsapp' => '6281200000000',
                    'items' => array_map(fn (int $i) => [
                        'title' => "Keperluan {$i}",
                        'desc' => $this->faker->sentence(8),
                        'message' => "Shalom, saya ingin bertanya tentang keperluan {$i}.",
                    ], range(1, 6)),
                ],
            ],
            'Visit' => [
                'meta' => $this->meta('Kunjungi Kami'),
                'hero' => [
                    'title' => $this->title(6),
                    'subtitle' => $this->faker->sentence(12),
                    'image' => $this->image('visit-page-hero', 'Kunjungi · Hero', 1920, 1080),
                ],
                'expect' => [
                    'title' => $this->title(4),
                    'items' => array_map(fn () => ['title' => $this->title(3), 'desc' => $this->faker->sentence(14)], range(1, 4)),
                ],
                'arrival' => [
                    'title' => $this->title(4),
                    'parking' => $this->faker->sentence(16),
                    'steps' => array_map(fn (int $i) => [
                        'title' => "Langkah {$i}: ".$this->title(3),
                        'desc' => $this->faker->sentence(14),
                        'image' => $this->image('visit-page-arrival', "Langkah {$i}", 800, 520),
                    ], range(1, 3)),
                ],
                'welcome' => [
                    'title' => $this->title(5),
                    'desc' => $this->faker->paragraph(3),
                    'button_text' => 'Tombol kabari kami',
                    'button_link' => 'https://wa.me/6281200000000?text=Shalom%2C%20saya%20akan%20datang.',
                ],
                'faq' => $this->faq(),
            ],
        ];
    }

    private function title(int $words = 5): string
    {
        return rtrim($this->faker->sentence($words, false), '.');
    }

    private function list(): string
    {
        return '<ol><li>'.implode('</li><li>', $this->faker->sentences(3)).'</li></ol>';
    }

    private function article(): string
    {
        return '<p>'.implode('</p><p>', $this->faker->paragraphs(2)).'</p>'
            .'<h2>'.$this->title(4).'</h2>'
            .'<p>'.implode('</p><p>', $this->faker->paragraphs(2)).'</p>'
            .'<blockquote><p>'.$this->faker->sentence(16).'</p><cite>Kitab 1:1</cite></blockquote>'
            .'<p>'.$this->faker->paragraph(4).'</p>';
    }

    private function faq(): array
    {
        return [
            'title' => $this->title(4),
            'questionsList' => array_map(fn () => [
                'question' => rtrim($this->faker->sentence(7), '.').'?',
                'answer' => '<p>'.$this->faker->paragraph(3).'</p>',
            ], range(1, 4)),
        ];
    }

    private function meta(string $page): array
    {
        return [
            'title' => "{$page} — Nama Gereja",
            'description' => Str::limit($this->faker->paragraph(3), 155),
            'image' => $this->image('seo', "SEO · {$page}", 1200, 630),
        ];
    }

    /**
     * Writes a labelled placeholder to the public disk and returns its path.
     * GD's bitmap font is drawn on a small canvas and scaled up so the label stays legible.
     */
    private function image(string $directory, string $label, int $width, int $height, bool $transparent = false): string
    {
        $path = "{$directory}/placeholder-".Str::slug($label).($transparent ? '.png' : '.jpg');

        $image = imagecreatetruecolor($width, $height);
        [$r, $g, $b] = sscanf(self::COLORS[crc32($path) % count(self::COLORS)], '#%02x%02x%02x');
        $background = $transparent ? imagecolorallocatealpha($image, 0, 0, 0, 127) : imagecolorallocate($image, $r, $g, $b);
        $ink = $transparent ? imagecolorallocate($image, $r, $g, $b) : imagecolorallocatealpha($image, 255, 255, 255, 90);
        imagealphablending($image, false);
        imagefill($image, 0, 0, $background);
        imagealphablending($image, true);
        imagesavealpha($image, true);
        imagesetthickness($image, max(1, intdiv($width, 400)));
        imageline($image, 0, 0, $width, $height, $ink);
        imageline($image, 0, $height, $width, 0, $ink);

        // The bitmap font is ASCII-only.
        $text = str_replace('·', '-', "{$label} · {$width}x{$height}");
        $labelWidth = imagefontwidth(5) * strlen($text) + 12;
        $labelHeight = imagefontheight(5) + 8;
        $canvas = imagecreatetruecolor($labelWidth, $labelHeight);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagefill($canvas, 0, 0, $transparent ? imagecolorallocatealpha($canvas, 0, 0, 0, 127) : imagecolorallocate($canvas, $r, $g, $b));
        imagestring($canvas, 5, 6, 4, $text, $transparent ? imagecolorallocate($canvas, $r, $g, $b) : imagecolorallocate($canvas, 255, 255, 255));

        $scale = max(1, (int) floor(min($width * 0.85 / $labelWidth, $height * 0.3 / $labelHeight)));
        imagecopyresized(
            $image, $canvas,
            intdiv($width - $labelWidth * $scale, 2), intdiv($height - $labelHeight * $scale, 2),
            0, 0, $labelWidth * $scale, $labelHeight * $scale, $labelWidth, $labelHeight,
        );

        ob_start();
        $transparent ? imagepng($image) : imagejpeg($image, null, 82);
        Storage::disk('public')->put($path, ob_get_clean());

        return $path;
    }
}
