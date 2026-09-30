<?php

namespace Database\Seeders;

use App\Models\Devotion;
use App\Models\Division;
use App\Models\Pages;
use App\Models\Pastor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Mockup content for development: the church's basic facts (name, address, motto,
 * service times, divisions, senior pastors) plus made-up copy that reads like a
 * real website, and generated placeholder images for every image field.
 * Overwrites page content; records are keyed by title/slug, so re-running updates them.
 * PageSeeder holds the real production content.
 *
 * php artisan db:seed --class=PlaceholderSeeder
 */
class PlaceholderSeeder extends Seeder
{
    private const COLORS = ['#173f55', '#27617f', '#1f5046', '#6b4f2a', '#4a3f6b'];

    private const CHURCH = 'Hebron Family Church';

    private const MOTTO = 'United as One Family to Live like Christ';

    private const WHATSAPP = '6281387243736';

    public function run(): void
    {
        foreach ($this->pages() as $pageName => $sections) {
            $page = Pages::firstOrCreate(['name' => $pageName]);
            foreach ($sections as $name => $content) {
                $page->sections()->updateOrCreate(['name' => $name], ['content' => $content]);
            }
        }

        foreach ($this->divisions() as $sort => [$audience, $title, $time, $shortTime, $description]) {
            Division::updateOrCreate(['title' => $title], [
                'audience' => $audience,
                'description' => $description,
                'image' => $this->image('divisions-image', $title, 800, 1000),
                'time' => $time,
                'short_time' => $shortTime,
                'link' => 'https://www.instagram.com/gpdigadingserpong',
                'sort' => $sort,
            ]);
        }

        Pages::firstWhere('name', 'Visit')->sections()->updateOrCreate(['name' => 'kids'], ['content' => [
            'title' => 'Ada ibadah untuk seluruh keluarga',
            'desc' => 'Selain Ibadah Raya, setiap generasi punya ibadah dan komunitasnya sendiri. Bawa anak? Seeds of Truth melayani anak dari balita hingga sebelum SMP, bersamaan dengan Ibadah Raya 2 pukul 10.00.',
            'division_ids' => Division::whereIn('title', ['Seeds of Truth', 'Descendants of Truth'])->pluck('id')->all(),
        ]]);

        foreach ([
            ['Pdt. Raimond Sumaa & Pdt. Yesie Sumaa', 'Gembala Sidang'],
            ['Pdm. Daniel Wijaya', 'Gembala Pemuda & Mahasiswa'],
            ['Pdm. Ruth Hutapea', 'Gembala Anak & Keluarga'],
            ['Ev. Samuel Tanoto', 'Koordinator Pelayanan'],
        ] as $i => [$name, $position]) {
            Pastor::updateOrCreate(['name' => $name], [
                'position' => $position,
                'image' => $this->image('pastors-image', 'Gembala '.($i + 1), 800, 1000),
            ]);
        }

        foreach ($this->devotions() as $i => $devotion) {
            Devotion::updateOrCreate(['slug' => Str::slug($devotion['title'])], [
                ...$devotion,
                'image' => $this->image('devotion-image', 'Renungan '.($i + 1), 1600, 900),
                'published_at' => now()->subWeeks($i)->toDateString(),
            ]);
        }
    }

    private function pages(): array
    {
        return [
            'Settings' => [
                'general_settings' => [
                    'church_name' => self::CHURCH,
                    'church_logo' => $this->image('site-settings', 'Logo', 240, 240, transparent: true),
                    'church_address' => '<p>Kompleks Pasar Modern Paramount Blok L39, Jl. Boulevard Raya Gading Serpong, Curug Sangereng, Kelapa Dua, Kab. Tangerang, Banten 15810</p>',
                    'maps_url' => 'https://maps.app.goo.gl/GoMhnkkmL2A7wp6c9',
                    'livestream_url' => 'https://www.instagram.com/gpdigadingserpong/live',
                    'social_media' => [
                        ['platform' => 'Instagram', 'url' => 'https://www.instagram.com/gpdigadingserpong'],
                        ['platform' => 'YouTube', 'url' => 'https://www.youtube.com/@hebronfamilychurch'],
                        ['platform' => 'Facebook', 'url' => 'https://www.facebook.com/hebronfamilychurch'],
                    ],
                ],
            ],
            'Home' => [
                'meta' => $this->meta('Beranda', 'Gereja keluarga di Gading Serpong. Ibadah Raya setiap Minggu pukul 07.30, 10.00, dan 17.30 WIB. Datang dan jadilah bagian dari keluarga Hebron.'),
                'hero' => [
                    'title' => self::MOTTO,
                    'image' => $this->image('home-page-hero', 'Home · Hero', 1920, 1080),
                    'video' => null,
                    'primary_button' => ['text' => 'Kunjungi Ibadah Kami', 'link' => '/visit'],
                    'secondary_button' => ['text' => 'Kenali Hebron', 'link' => '/about'],
                ],
                'vision_mission' => [
                    'title' => 'Visi & Misi',
                    'visi' => ['title' => 'Visi Kami', 'desc' => $this->vision()],
                    'misi' => ['title' => 'Misi Kami', 'desc' => $this->mission()],
                    'about_button_text' => 'Selengkapnya tentang Hebron',
                ],
                // 1–4: gallery, 5–7: Pelayanan, Renungan and Hubungi Kami cards.
                'carousel' => array_map(
                    fn (string $label) => $this->image('home-page-carousel', $label, 1200, 1500),
                    ['Ibadah Raya', 'Pujian Penyembahan', 'Kebersamaan Jemaat', 'Ibadah Anak', 'Tim Pelayanan', 'Saat Teduh', 'Tim Penyambut'],
                ),
                'schedule' => [
                    'title' => 'Ibadah Raya',
                    'services' => [
                        ['title' => 'Ibadah Raya 1', 'time' => '07.30', 'livestream' => false, 'note' => 'Dilanjutkan Kantin Hebron setelah ibadah.'],
                        ['title' => 'Ibadah Raya 2', 'time' => '10.00', 'livestream' => true, 'note' => 'Seeds of Truth untuk anak di jam yang sama. Dilanjutkan Kantin Hebron.'],
                        ['title' => 'Ibadah Raya 3', 'time' => '17.30', 'livestream' => false, 'note' => 'Ibadah sore dengan susunan yang sama, cocok setelah akhir pekan yang padat.'],
                    ],
                    'division_button_text' => 'Lihat semua divisi',
                ],
                'pastor_greeting' => [
                    'subtitle' => 'Sambutan dari gembala kami',
                    'pastors_name' => 'Pdt. Raimond Sumaa & Pdt. Yesie Sumaa',
                    'pastors_position' => 'Gembala Sidang',
                    'pastor_img' => $this->image('home-page-pastor', 'Foto Gembala', 1000, 1300),
                    'pastor_signature' => $this->image('home-page-pastor', 'Tanda Tangan', 600, 200, transparent: true),
                    'desc' => '<p>"Gereja bukan gedung, melainkan keluarga. Di Hebron, kami rindu setiap orang yang datang merasa diterima, bertumbuh, dan menemukan panggilannya di dalam Kristus. Selamat datang di rumah."</p>',
                    'pastoral_button_text' => 'Lihat tim pastoral Hebron',
                ],
            ],
            'About' => [
                'meta' => $this->meta('Tentang Kami', 'Kenali visi, misi, nilai, dan pengakuan iman Hebron Family Church, GPdI Hebron Gading Serpong, serta tim pastoral kami.'),
                'hero' => [
                    'title' => self::MOTTO,
                    'subtitle' => 'Sejak awal berdiri, Hebron rindu menjadi tempat persekutuan, kota perlindungan, dan tanah yang subur bagi setiap generasi.',
                    'image' => $this->image('about-page-hero', 'Tentang · Hero', 1920, 1080),
                ],
                'vision_mission' => [
                    'visi' => ['title' => 'Visi Kami', 'desc' => $this->vision()],
                    'misi' => ['title' => 'Misi Kami', 'desc' => $this->mission()],
                    'image' => $this->image('about-page-vision-mission', 'Visi & Misi', 1200, 1500),
                ],
                'values' => [
                    ['icon' => 'lucide:flame', 'title' => 'Holiness', 'desc' => '<p><strong>Ibrani 12:14</strong></p><p>Hidup dekat dengan Firman<br>Peka akan pimpinan Roh Kudus<br>Tekun dalam doa dan penyembahan</p>'],
                    ['icon' => 'lucide:heart-handshake', 'title' => 'Obedience', 'desc' => '<p><strong>Yohanes 14:23</strong></p><p>Bertumbuh dalam karakter<br>Setia pada komitmen<br>Menjadi teladan di rumah dan pekerjaan</p>'],
                    ['icon' => 'lucide:hand-heart', 'title' => 'Ministry', 'desc' => '<p><strong>Roma 12:11</strong></p><p>Melayani keluarga<br>Melayani komunitas dan sesama<br>Melayani dengan sukacita</p>'],
                    ['icon' => 'lucide:globe', 'title' => 'Evangelism', 'desc' => '<p><strong>Matius 28:19-20</strong></p><p>Membagikan Injil<br>Memuridkan<br>Menjangkau setiap generasi</p>'],
                ],
                'beliefs' => [
                    'title' => 'Apa yang kami percaya',
                    'intro' => '<p>Sebagai bagian dari Gereja Pantekosta di Indonesia (GPdI), kami berdiri di atas Pengakuan Iman GPdI. Berikut ringkasan pokok-pokok iman yang kami pegang bersama.</p>',
                    'link' => 'https://gpdi.or.id',
                    'items' => [
                        ['title' => 'Alkitab', 'desc' => 'Alkitab adalah Firman Allah yang diilhamkan dan menjadi dasar iman serta kehidupan kita.', 'verse' => '2 Timotius 3:16'],
                        ['title' => 'Allah Tritunggal', 'desc' => 'Satu Allah yang kekal, yang menyatakan diri sebagai Bapa, Anak, dan Roh Kudus.', 'verse' => 'Matius 28:19'],
                        ['title' => 'Yesus Kristus', 'desc' => 'Anak Allah yang menjadi manusia, mati bagi dosa kita, bangkit, dan naik ke surga.', 'verse' => 'Yohanes 1:14'],
                        ['title' => 'Keselamatan', 'desc' => 'Anugerah Allah yang diterima oleh iman kepada Yesus Kristus, bukan karena perbuatan.', 'verse' => 'Efesus 2:8-9'],
                        ['title' => 'Baptisan Air', 'desc' => 'Setiap orang percaya dibaptis secara selam sebagai tanda hidup baru di dalam Kristus.', 'verse' => 'Kisah Para Rasul 2:38'],
                        ['title' => 'Baptisan Roh Kudus', 'desc' => 'Roh Kudus dicurahkan untuk memberi kuasa bersaksi dan hidup kudus.', 'verse' => 'Kisah Para Rasul 1:8'],
                        ['title' => 'Kesembuhan Ilahi', 'desc' => 'Oleh bilur-bilur Kristus tersedia kesembuhan bagi tubuh, jiwa, dan roh.', 'verse' => 'Yesaya 53:5'],
                        ['title' => 'Kedatangan Kristus', 'desc' => 'Yesus Kristus akan datang kembali untuk menjemput gereja-Nya.', 'verse' => '1 Tesalonika 4:16-17'],
                    ],
                ],
            ],
            'Ministries Page' => [
                'meta' => $this->meta('Pelayanan', 'Temukan tempatmu melayani di Hebron Family Church: Praise & Worship, Production, Service & Hospitality, Creative Production, dan Prayer.'),
                'hero' => [
                    'title' => 'Melayani dengan talenta yang Tuhan berikan',
                    'image' => $this->image('ministry-page-hero', 'Pelayanan · Hero', 1920, 1080),
                ],
                'intro' => [
                    'title' => 'Mari melayani Tuhan bersama',
                    'desc' => '<p>Pelayanan adalah ungkapan kasih dan syukur kita kepada Tuhan. Setiap Minggu, lebih dari seratus relawan menyiapkan ibadah, menyambut jemaat baru, mengajar anak-anak, dan memastikan siaran berjalan lancar. Apa pun talentamu, selalu ada tempat untuk ikut ambil bagian.</p>',
                ],
                'serve' => [
                    'title' => 'Wadah pelayanan kami',
                    'ministryList' => array_map(fn (array $ministry) => [
                        'title' => $ministry[0],
                        'desc' => '<p>- '.implode('<br>- ', $ministry[1]).'</p>',
                        'image' => $this->image('ministry-page-ministries', $ministry[0], 1200, 900),
                        'link' => 'https://forms.gle/8Hc5VSgDXeU9NYB86',
                    ], [
                        ['Praise & Worship', ['Singer', 'Musik', 'Sound Engineer']],
                        ['Production', ['Multimedia & Live Streaming', 'Desain & Editing', 'Media Sosial', 'Fotografi & Dokumentasi']],
                        ['Service & Hospitality', ['Usher & Penyambut', 'Perjamuan Kudus', 'Stage Manager']],
                        ['Creative Production', ['Event Planner', 'Event Coordinator']],
                        ['Prayer', ['Doa Jumat', 'Doa Sepekan', 'Tim Doa Ibadah Raya']],
                    ]),
                ],
                'faq' => [
                    'title' => 'Bergabung bersama kami',
                    'questionsList' => [
                        ['question' => 'Siapa saja yang bisa bergabung melayani?', 'answer' => '<p>Setiap jemaat yang sudah lahir baru dan rindu melayani bisa bergabung. Tidak perlu berpengalaman, kami akan memperlengkapimu.</p>'],
                        ['question' => 'Bagaimana cara mendaftar?', 'answer' => '<p>Pilih pelayanan yang kamu minati, lalu isi formulir pendaftaran. Koordinator akan menghubungimu dalam satu sampai dua minggu.</p>'],
                        ['question' => 'Apakah ada pelatihan untuk pelayan baru?', 'answer' => '<p>Ya. Setiap tim punya masa orientasi dan latihan rutin, biasanya setiap Sabtu sore.</p>'],
                        ['question' => 'Berapa lama komitmen melayani?', 'answer' => '<p>Jadwal diatur bergiliran. Kami mengajak setiap pelayan berkomitmen minimal satu tahun supaya bisa bertumbuh bersama tim.</p>'],
                    ],
                ],
            ],
            'Division Page' => [
                'meta' => $this->meta('Divisi', 'Ibadah untuk setiap generasi: anak, remaja, mahasiswa, profesional muda, keluarga muda, kaum pria, dan kaum wanita.'),
                'hero' => [
                    'title' => 'Ibadah untuk setiap generasi',
                    'image' => $this->image('division-page-hero', 'Divisi · Hero', 1920, 1080),
                ],
            ],
            'Contact' => [
                'meta' => $this->meta('Kontak', 'Hubungi sekretariat Hebron Family Church untuk informasi, permohonan doa, konseling, baptisan, penyerahan anak, dan pemberkatan nikah.'),
                'hero' => [
                    'title' => 'Hubungi kami',
                    'subtitle' => 'Pertanyaan, permohonan doa, atau kebutuhan pelayanan, kami siap membantu.',
                    'image' => $this->image('contact-page-hero', 'Kontak · Hero', 1920, 1080),
                ],
                'contact_list' => [
                    ['title' => 'Sekretariat', 'desc' => '0813 8724 3736 / 0821 9420 0174', 'link' => null],
                    ['title' => 'Email', 'desc' => 'sekretariat@gpdi-hebron.com', 'link' => 'mailto:sekretariat@gpdi-hebron.com'],
                    ['title' => 'Instagram', 'desc' => '@gpdigadingserpong', 'link' => 'https://www.instagram.com/gpdigadingserpong'],
                ],
                'office_hours' => ['hours' => 'Selasa – Sabtu · 08.00 – 16.00 WIB'],
                'requests' => [
                    'title' => 'Kami bisa bantu',
                    'whatsapp' => self::WHATSAPP,
                    'items' => [
                        ['title' => 'Informasi umum', 'desc' => 'Jadwal, kegiatan, atau hal lain seputar gereja.', 'message' => 'Shalom, saya ingin bertanya tentang Hebron Family Church.'],
                        ['title' => 'Permohonan doa', 'desc' => 'Ceritakan pergumulanmu, tim doa kami akan mendoakanmu.', 'message' => 'Shalom, saya ingin mengajukan permohonan doa.'],
                        ['title' => 'Konseling pastoral', 'desc' => 'Buat janji untuk berbicara dengan gembala.', 'message' => 'Shalom, saya ingin membuat janji konseling pastoral.'],
                        ['title' => 'Baptisan air', 'desc' => 'Daftar kelas persiapan dan jadwal baptisan berikutnya.', 'message' => 'Shalom, saya ingin mendaftar baptisan air.'],
                        ['title' => 'Penyerahan anak', 'desc' => 'Serahkan buah hatimu kepada Tuhan dalam Ibadah Raya.', 'message' => 'Shalom, saya ingin mendaftarkan penyerahan anak.'],
                        ['title' => 'Pemberkatan nikah', 'desc' => 'Bimbingan pranikah dan jadwal pemberkatan.', 'message' => 'Shalom, saya ingin bertanya tentang pemberkatan nikah.'],
                    ],
                ],
            ],
            'Visit' => [
                'meta' => $this->meta('Kunjungi Kami', 'Panduan ibadah pertamamu di Hebron Family Church, Gading Serpong: jadwal Ibadah Raya, cara sampai, parkir, dan ibadah anak.'),
                'hero' => [
                    'title' => 'Minggu ini, ada tempat untukmu',
                    'subtitle' => 'Pertama kali ke Hebron? Ini yang perlu kamu tahu sebelum datang.',
                    'image' => $this->image('visit-page-hero', 'Kunjungi · Hero', 1920, 1080),
                ],
                'expect' => [
                    'title' => 'Apa yang akan kamu alami',
                    'items' => [
                        ['title' => 'Sekitar 2 jam', 'desc' => 'Setiap Ibadah Raya berlangsung paling lama sekitar dua jam.'],
                        ['title' => 'Pujian, Firman, doa', 'desc' => 'Ibadah dibuka dengan pujian penyembahan, lalu khotbah Firman Tuhan dan doa bersama.'],
                        ['title' => 'Sama di setiap jam', 'desc' => 'Ketiga Ibadah Raya punya susunan dan gaya yang sama. Pilih jam yang paling pas untukmu.'],
                        ['title' => 'Kantin Hebron', 'desc' => 'Setelah Ibadah Raya 1 dan 2, mampir ke Kantin Hebron untuk makanan ringan dan bersekutu bersama jemaat.'],
                    ],
                ],
                'arrival' => [
                    'title' => 'Cara sampai ke Hebron',
                    'parking' => 'Area depan gereja adalah lahan parkir umum. Datang 15 menit lebih awal supaya lebih mudah mendapat tempat.',
                    'steps' => [
                        ['title' => 'Masuk ke Pasar Modern Paramount', 'desc' => 'Dari Jl. Boulevard Raya Gading Serpong, belok masuk ke kompleks Pasar Modern Paramount.', 'image' => $this->image('visit-page-arrival', 'Pintu Masuk Pasar', 800, 520)],
                        ['title' => 'Cari Blok L39', 'desc' => 'Ikuti deretan ruko Blok L. Gereja ada di nomor 39, dengan papan nama Hebron di depannya.', 'image' => $this->image('visit-page-arrival', 'Deretan Blok L', 800, 520)],
                        ['title' => 'Parkir dan masuk', 'desc' => 'Parkir di area depan gereja, lalu masuk. Tim penyambut akan menyapamu di pintu.', 'image' => $this->image('visit-page-arrival', 'Pintu Gereja', 800, 520)],
                    ],
                ],
                'welcome' => [
                    'title' => 'Kabari kami kamu akan datang',
                    'desc' => 'Tim penyambut kami akan menyapamu saat tiba, membantu mencarikan tempat duduk, dan mengenalkanmu pada beberapa jemaat setelah ibadah.',
                    'button_text' => 'Kabari kami via WhatsApp',
                    'button_link' => 'https://wa.me/'.self::WHATSAPP.'?text='.rawurlencode('Shalom, saya berencana datang ke ibadah Hebron hari Minggu ini.'),
                ],
                'faq' => [
                    'title' => 'Pertanyaan yang sering diajukan',
                    'questionsList' => [
                        ['question' => 'Bolehkah saya datang sendiri?', 'answer' => '<p>Tentu. Banyak jemaat pertama kali datang sendirian. Tim penyambut akan membantumu menemukan tempat duduk.</p>'],
                        ['question' => 'Apa yang sebaiknya saya pakai?', 'answer' => '<p>Pakailah pakaian yang rapi dan nyaman. Tidak ada aturan khusus.</p>'],
                        ['question' => 'Usia berapa yang ikut Seeds of Truth?', 'answer' => '<p>Seeds of Truth melayani anak dari balita hingga sebelum SMP, bersamaan dengan Ibadah Raya 2 pukul 10.00 WIB.</p>'],
                        ['question' => 'Bagaimana jika saya belum bisa datang?', 'answer' => '<p>Ibadah Raya 2 pukul 10.00 WIB disiarkan live streaming melalui Instagram <a href="https://www.instagram.com/gpdigadingserpong">@gpdigadingserpong</a>.</p>'],
                    ],
                ],
            ],
        ];
    }

    /** @return array<int, array{string, string, string, string, string}> audience, title, time, short time, description */
    private function divisions(): array
    {
        return [
            ['Anak', 'Seeds of Truth', 'Setiap Minggu · 10.00 WIB', 'Min 10.00', 'Ibadah anak dari balita hingga sebelum SMP. Lewat cerita Alkitab, pujian, dan permainan, anak-anak belajar mengenal Yesus sesuai usianya.'],
            ['Remaja', 'Descendants of Truth', 'Setiap Sabtu · 13.30 WIB', 'Sab 13.30', 'Komunitas remaja SMP dan SMA untuk bertumbuh bersama, berani bertanya, dan menemukan identitas di dalam Kristus.'],
            ['Mahasiswa', 'Speaker of Truth', 'Setiap Sabtu · 16.30 WIB', 'Sab 16.30', 'Tempat mahasiswa saling menguatkan di tengah kuliah, organisasi, dan pencarian panggilan hidup.'],
            ['Profesional Muda', 'Pillars of Truth', 'Minggu, dua pekan sekali · 14.00 WIB', 'Min 14.00', 'Persekutuan untuk para pekerja muda yang ingin membawa nilai Kerajaan Allah ke dunia kerja.'],
            ['Keluarga Muda', 'Family of Christ Community', 'Sabtu pertama setiap bulan · 18.00 WIB', 'Sab 18.00', 'Komunitas pasangan dan keluarga muda untuk belajar membangun rumah tangga yang berpusat pada Kristus.'],
            ['Kaum Pria', 'Men of God', 'Jumat kedua setiap bulan · 19.30 WIB', 'Jum 19.30', 'Persekutuan kaum pria untuk bertumbuh sebagai imam dalam keluarga dan teladan di tengah masyarakat.'],
            ['Kaum Wanita', 'Women of God', 'Kamis ketiga setiap bulan · 10.00 WIB', 'Kam 10.00', 'Wadah para wanita untuk saling mendoakan, belajar Firman, dan melayani bersama.'],
        ];
    }

    /** @return array<int, array{title: string, category: string, content: string}> newest first */
    private function devotions(): array
    {
        return [
            [
                'title' => 'Berakar di Tengah Musim Kering',
                'category' => 'devotion',
                'content' => '<p><strong>Yeremia 17:7-8</strong> — "Diberkatilah orang yang mengandalkan TUHAN... Ia akan seperti pohon yang ditanam di tepi air."</p><p>Pohon di tepi air tidak berhenti berbuah ketika musim kering datang. Rahasianya bukan cuaca yang bersahabat, melainkan akar yang terus menjangkau sumber air.</p><h2>Akar yang tidak terlihat</h2><p>Yang orang lain lihat adalah daun dan buah. Yang Tuhan lihat adalah akar: waktu teduh yang setia, doa yang jujur, dan ketaatan dalam hal kecil. Musim kering justru menunjukkan seberapa dalam akar itu.</p><blockquote><p>Musim tidak menentukan buahmu. Sumber airmu yang menentukan.</p><cite>Renungan Minggu Ini</cite></blockquote><p><em>Renungkan: Di mana akar hidupmu tertanam minggu ini?</em></p>',
            ],
            [
                'title' => 'Kantin Hebron Kini Buka Setelah Ibadah Raya 2',
                'category' => 'news',
                'content' => '<p>Mulai bulan ini, Kantin Hebron juga buka setelah Ibadah Raya 2. Tersedia nasi kotak, kue, kopi, dan teh dengan harga terjangkau.</p><p>Seluruh hasil penjualan digunakan untuk mendukung kegiatan divisi anak dan remaja. Terima kasih kepada tim Service & Hospitality yang melayani setiap Minggu.</p><p>Sampai jumpa di Kantin Hebron!</p>',
            ],
            [
                'title' => 'Pendaftaran Baptisan Air Periode Berikutnya',
                'category' => 'announcement',
                'content' => '<p>Bagi jemaat yang rindu mengambil langkah iman melalui baptisan air, pendaftaran periode berikutnya sudah dibuka.</p><h2>Alur pendaftaran</h2><ol><li>Hubungi sekretariat melalui WhatsApp.</li><li>Ikuti dua kali kelas persiapan pada hari Sabtu.</li><li>Baptisan dilaksanakan setelah Ibadah Raya 2.</li></ol><p>Informasi lengkap dapat ditanyakan di <a href="/contact">halaman kontak</a>.</p>',
            ],
            [
                'title' => 'Satu Keluarga di dalam Kristus',
                'category' => 'devotion',
                'content' => '<p><strong>Efesus 2:19</strong> — "Kamu bukan lagi orang asing dan pendatang, melainkan kawan sewarga dari orang-orang kudus dan anggota-anggota keluarga Allah."</p><p>Di dalam Kristus, kita tidak dipanggil berjalan sendiri. Allah menempatkan kita di dalam keluarga rohani tempat kita saling menguatkan dan bertumbuh bersama.</p><p>Keluarga rohani bukan tentang kesempurnaan, melainkan kesetiaan untuk tetap hadir satu bagi yang lain. Minggu ini, sapalah seseorang yang belum kamu kenal di ibadah.</p><p><em>Doa: Tuhan, ajar aku mengasihi saudara-saudaraku seperti Engkau mengasihiku. Amin.</em></p>',
            ],
            [
                'title' => 'Retret Remaja DOTS: Rooted',
                'category' => 'news',
                'content' => '<p>Lebih dari enam puluh remaja mengikuti retret DOTS bertema <em>Rooted</em> di Puncak selama tiga hari. Sesi Firman, games, dan malam penyembahan menjadi momen banyak remaja memperbarui komitmen mereka.</p><p>Terima kasih kepada para orang tua dan pembina yang mendukung acara ini. Foto-foto retret dapat dilihat di Instagram gereja.</p>',
            ],
            [
                'title' => 'Doa Puasa Bersama Awal Bulan',
                'category' => 'announcement',
                'content' => '<p>Seluruh jemaat diajak mengikuti doa puasa bersama pada tiga hari pertama bulan depan. Doa bersama diadakan setiap malam pukul 19.00 WIB di gedung gereja dan melalui Zoom.</p><p>Pokok doa akan dibagikan melalui grup WhatsApp divisi masing-masing.</p>',
            ],
        ];
    }

    private function vision(): string
    {
        return '<p>Menjadi keluarga yang bertumbuh di dalam kesatuan berlandaskan kasih, membangun gereja sebagai rumah bagi setiap generasi, sehingga memiliki karakter Kristus.</p>';
    }

    private function mission(): string
    {
        return '<ol><li>Menghidupi Firman Allah dalam pengurapan Roh Kudus</li><li>Memperlengkapi jemaat dalam talenta, karunia, dan kreativitas untuk memenuhi panggilan Tuhan</li><li>Melayani generasi dalam pembapaan dan pemuridan</li></ol>';
    }

    private function meta(string $page, string $description): array
    {
        return [
            'title' => "{$page} — ".self::CHURCH,
            'description' => $description,
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
