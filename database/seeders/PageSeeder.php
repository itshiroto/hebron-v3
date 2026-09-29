<?php

namespace Database\Seeders;

use App\Models\Devotion;
use App\Models\Division;
use App\Models\Pages;
use App\Models\Pastor;
use Illuminate\Database\Seeder;

/**
 * Page content based on the live site (https://gpdi-hebron.com), with the gaps
 * (SEO, beliefs, FAQ, division schedules, devotions, …) filled with drafted copy.
 * Image paths point at the public disk; copy storage/app/public from production to render them.
 */
class PageSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->pages() as $pageName => $sections) {
            $page = Pages::firstOrCreate(['name' => $pageName]);

            foreach ($sections as $name => $content) {
                $page->sections()->updateOrCreate(['name' => $name], ['content' => $content]);
            }
        }

        foreach ([
            [
                'name' => 'Ps. Raimond Sumaa & Ps. Yesie Sumaa',
                'position' => 'Gembala Sidang',
                'image' => 'pastors-image/01K4G474BJ8W8MN7XPY8GDN7C9.jpg',
            ],
        ] as $pastor) {
            Pastor::updateOrCreate(['name' => $pastor['name']], $pastor);
        }

        foreach ([
            [
                'title' => 'SPoT',
                'audience' => 'Mahasiswa & Young Professional',
                'description' => 'College & Young Pro Community',
                'image' => 'divisions-image/01K42K1GAGYD9CE92R13EVBWEM.jpg',
                'time' => 'Setiap Sabtu · 16.30 WIB',
                'short_time' => 'Sab 16.30',
                'link' => 'https://www.instagram.com/spot_gen?igsh=ZzJ6bTIya3J1YWdx',
                'sort' => 0,
            ],
            [
                'title' => 'DoT\'s',
                'audience' => 'Remaja',
                'description' => 'Descendants Of Truth\'s',
                'image' => 'divisions-image/01K42JNDFT12PC0JCV349PXMW6.jpg',
                'time' => 'Setiap Sabtu · 14.00 WIB',
                'short_time' => 'Sab 14.00',
                'link' => 'https://www.instagram.com/dots_gen?igsh=bGF1NXAzNXZtbGpt',
                'sort' => 1,
            ],
            [
                'title' => 'SoT',
                'audience' => 'Anak',
                'description' => 'Seeds Of Truth',
                'image' => 'divisions-image/01K42K067AXAH5CFN7MMQTDFTY.jpg',
                'time' => 'Setiap Minggu · 10.00 WIB',
                'short_time' => 'Min 10.00',
                'link' => 'https://www.instagram.com/seedsoftruth__?igsh=azlwa3FkNm1oMHd2',
                'sort' => 2,
            ],
        ] as $division) {
            Division::updateOrCreate(['title' => $division['title']], $division);
        }

        foreach ($this->devotions() as $devotion) {
            Devotion::updateOrCreate(['slug' => $devotion['slug']], $devotion);
        }
    }

    private function pages(): array
    {
        return [
            'Settings' => [
                'general_settings' => [
                    'church_name' => 'Hebron Family Church',
                    'church_subtitle' => 'Gading Serpong',
                    'church_logo' => 'site-settings/01K21F4PSXXQRG1567RKR2ZPXP.png',
                    'church_address' => '<p>Kompleks Pasar Modern Paramount Blok L39, Jl. Boulevard Raya Gading Serpong,<br>Curug Sangereng, Kelapa Dua,<br>Kabupaten Tangerang, Banten 15810</p>',
                    'maps_url' => 'https://maps.app.goo.gl/GoMhnkkmL2A7wp6c9',
                    'livestream_url' => 'https://www.instagram.com/gpdigadingserpong/live',
                    'social_media' => [
                        [
                            'platform' => 'Instagram',
                            'url' => 'https://www.instagram.com/gpdigadingserpong',
                        ],
                    ],
                ],
            ],
            'Home' => [
                'meta' => [
                    'title' => 'Hebron Family Church — GPdI Hebron Gading Serpong',
                    'description' => 'Gereja keluarga di Gading Serpong. Ibadah Raya setiap Minggu pukul 07.30, 10.00, dan 17.30 WIB. United as One Family to Live Like Christ.',
                    'image' => 'home-page-hero/01KN6ZAD5V4G7YR7XW5BX8K7BN.jpg',
                ],
                'hero' => [
                    'title' => 'God\'s Heart — HFC Good Friday & Easter Service',
                    'image' => 'home-page-hero/01KN6ZAD5V4G7YR7XW5BX8K7BN.jpg',
                    'primary_button' => [
                        'text' => 'Kunjungi Ibadah Kami',
                        'link' => 'https://maps.app.goo.gl/GoMhnkkmL2A7wp6c9',
                    ],
                    'secondary_button' => [
                        'text' => 'Tentang Hebron',
                        'link' => '/about',
                    ],
                ],
                'vision_mission' => [
                    'title' => 'Hebron Family Church, Tempat Persekutuan, Kota Perlindungan dan Tanah yang Subur',
                    'visi' => [
                        'title' => 'Visi Kami',
                        'desc' => '<p>Menjadi keluarga yang bertumbuh didalam kesatuan berlandaskan kasih untuk membangun gereja sebagai rumah bagi setiap generasi, sehingga memiliki Karakter Kristus</p>',
                    ],
                    'misi' => [
                        'title' => 'Misi Kami',
                        'desc' => '<ul><li>Menghidupi Firman Allah dalam pengurapan Roh Kudus</li><li>Memperlangkapi jemaat Tuhan dalam talenta, karunia, dan kreativitas untuk memenuhi panggilan Tuhan</li><li>Melayani generasi dalam pembapaan dan pemuridan</li></ul>',
                    ],
                    'about_button_text' => 'Selengkapnya tentang Hebron',
                ],
                'carousel' => [
                    'home-page-carousel/01K41T4APAHCPJPSE1XDGPDSY9.jpg',
                    'home-page-carousel/01K41T4APD74AFJNN9AZ2CCEWV.jpg',
                    'home-page-carousel/01K5222W3ZFFGMKNHXGQR16T91.jpeg',
                ],
                'schedule' => [
                    'title' => 'Ibadah Raya Hebron',
                    'subtitle' => 'Bertumbuh bersama kami',
                    'img' => 'home-page-schedule/01K41T4APF49NKGY31TK3YJZJ3.jpg',
                    'services' => [
                        [
                            'title' => 'Ibadah Raya 1',
                            'time' => '07.30',
                            'livestream' => false,
                        ],
                        [
                            'title' => 'Ibadah Raya 2',
                            'time' => '10.00',
                            'livestream' => true,
                        ],
                        [
                            'title' => 'Ibadah Raya 3',
                            'time' => '17.30',
                            'livestream' => false,
                        ],
                    ],
                    'division_button_text' => 'Lihat Ibadah Divisi Hebron',
                ],
                'pastor_greeting' => [
                    'subtitle' => 'Sambutan dari Gembala Kami',
                    'pastors_name' => 'Pdt. Raimond Sumaa & Pdt. Yesie Sumaa',
                    'pastor_img' => 'home-page-pastor/01K4CWD64XS1BNK1TQGB0Z8QSP.jpg',
                    'pastor_signature' => 'home-page-pastor/01K4CWGHWCJ87GAEHWMXMCTXFS.png',
                    'desc' => '<p>Selamat datang di Hebron Family Church! Kami rindu setiap orang yang datang menemukan keluarga, tempat untuk bertumbuh, dan perjumpaan pribadi dengan Tuhan Yesus. Siapa pun Anda dan dari mana pun Anda datang, ada tempat untuk Anda di sini.</p><p>"United as One Family, to Live Like Christ"</p>',
                    'pastoral_button_text' => 'Lihat tim Pastoral Hebron',
                ],
            ],
            'About' => [
                'meta' => [
                    'title' => 'Tentang Kami — Hebron Family Church',
                    'description' => 'Kenali visi, misi, nilai, dan pengakuan iman Hebron Family Church, GPdI Hebron Gading Serpong, serta para gembala kami.',
                    'image' => 'about-page-hero/01K4W8Y7YG05D8CFR1GH87XQNS.jpg',
                ],
                'hero' => [
                    'title' => 'United as One Family to Live Like Christ',
                    'subtitle' => 'Hebron Family Church, Tempat Persekutuan, Kota Perlindungan dan Tanah yang Subur',
                    'image' => 'about-page-hero/01K4W8Y7YG05D8CFR1GH87XQNS.jpg',
                ],
                'vision_mission' => [
                    'visi' => [
                        'title' => 'Visi Kami',
                        'desc' => '<p>Menjadi keluarga yang bertumbuh didalam kesatuan berlandaskan kasih untuk membangun gereja sebagai rumah bagi setiap generasi, sehingga memiliki Karakter Kristus</p>',
                    ],
                    'misi' => [
                        'title' => 'Misi Kami',
                        'desc' => '<ul><li>Menghidupi Firman Allah dalam pengurapan Roh Kudus</li><li>Memperlangkapi jemaat Tuhan dalam talenta, karunia, dan kreativitas untuk memenuhi panggilan Tuhan</li><li>Melayani generasi dalam pembapaan dan pemuridan</li></ul>',
                    ],
                    'image' => 'about-page-vision-mission/01K4W8Y7YPEVVX878P69TFCTYJ.jpg',
                ],
                'values' => [
                    [
                        'icon' => 'lucide:flame',
                        'title' => 'Holiness',
                        'desc' => '<p><strong>Ibrani 12:14</strong></p><p>Deep in words of God<br>Deep in the Holy Spirit<br>Deep in worship and prayer</p>',
                    ],
                    [
                        'icon' => 'lucide:heart-handshake',
                        'title' => 'Obedience',
                        'desc' => '<p><strong>Yohanes 14:23</strong></p><p>Grow in character and attitude<br>Grow in leadership (authority)<br>Grow in commitment and giving (keteladanan)</p>',
                    ],
                    [
                        'icon' => 'lucide:hand-heart',
                        'title' => 'Ministry',
                        'desc' => '<p><strong>Roma 12:11</strong></p><p>Serve families<br>Serve communities and others<br>Serve with enthusiasm</p>',
                    ],
                    [
                        'icon' => 'lucide:globe',
                        'title' => 'Evangelism',
                        'desc' => '<p><strong>Matius 28:19-20</strong></p><p>Share the Gospel<br>Make disciples<br>Reach every generation</p>',
                    ],
                ],
                'history' => [
                    [
                        'title' => 'Sejarah Hebron',
                        'desc' => '<p>Hebron Family Church bermula dari persekutuan kecil beberapa keluarga yang rindu menghadirkan gereja sebagai rumah bagi setiap generasi di Gading Serpong. Dengan pertolongan Tuhan, persekutuan ini bertumbuh menjadi jemaat GPdI Hebron yang melayani anak, remaja, pemuda, hingga keluarga.</p>',
                        'date' => '2006',
                        'image' => 'about-page-history/01K21G3YWDXNCNX7YCX36G2A5C.jpg',
                    ],
                ],
                'beliefs' => [
                    'title' => 'Apa yang kami percaya',
                    'intro' => '<p>Sebagai bagian dari Gereja Pantekosta di Indonesia (GPdI), kami berdiri di atas Pengakuan Iman GPdI. Berikut ringkasan pokok-pokok iman kami.</p>',
                    'items' => [
                        ['title' => 'Alkitab', 'desc' => 'Alkitab adalah Firman Allah yang diilhamkan, tanpa salah, dan menjadi dasar iman serta kehidupan kita.', 'verse' => '2 Timotius 3:16'],
                        ['title' => 'Allah Tritunggal', 'desc' => 'Ada satu Allah yang kekal, yang menyatakan diri sebagai Bapa, Anak, dan Roh Kudus.', 'verse' => 'Matius 28:19'],
                        ['title' => 'Yesus Kristus', 'desc' => 'Yesus adalah Anak Allah yang menjadi manusia, mati bagi dosa kita, bangkit, dan naik ke surga.', 'verse' => 'Yohanes 1:14'],
                        ['title' => 'Keselamatan', 'desc' => 'Keselamatan adalah anugerah Allah yang diterima oleh iman kepada Yesus Kristus, bukan karena perbuatan.', 'verse' => 'Efesus 2:8-9'],
                        ['title' => 'Baptisan Air', 'desc' => 'Setiap orang percaya dibaptis secara selam sebagai tanda pertobatan dan hidup baru di dalam Kristus.', 'verse' => 'Kisah Para Rasul 2:38'],
                        ['title' => 'Baptisan Roh Kudus', 'desc' => 'Roh Kudus dicurahkan kepada orang percaya untuk memberi kuasa bersaksi dan hidup kudus.', 'verse' => 'Kisah Para Rasul 1:8'],
                        ['title' => 'Kesembuhan Ilahi', 'desc' => 'Oleh bilur-bilur Kristus tersedia kesembuhan bagi tubuh, jiwa, dan roh.', 'verse' => 'Yesaya 53:5'],
                        ['title' => 'Kedatangan Kristus', 'desc' => 'Yesus Kristus akan datang kembali untuk menjemput gereja-Nya dan memerintah dalam kemuliaan.', 'verse' => '1 Tesalonika 4:16-17'],
                    ],
                ],
            ],
            'Contact' => [
                'meta' => [
                    'title' => 'Kontak — Hebron Family Church',
                    'description' => 'Hubungi sekretariat Hebron Family Church atau kunjungi kami di Kompleks Pasar Modern Paramount Blok L39, Gading Serpong, Tangerang.',
                    'image' => 'contact-page-hero/01K4W9SA4F13GQ49VW1QWT9Z33.jpg',
                ],
                'hero' => [
                    'title' => 'Contact Us',
                    'subtitle' => 'Kami senang bertemu denganmu. Hubungi kami atau datang langsung ke ibadah hari Minggu.',
                    'image' => 'contact-page-hero/01K4W9SA4F13GQ49VW1QWT9Z33.jpg',
                ],
                'contact_list' => [
                    [
                        'title' => 'Sekretariat',
                        'desc' => '081387243736/082194200174',
                        'link' => null,
                    ],
                    [
                        'title' => 'Instagram',
                        'desc' => '@gpdigadingserpong',
                        'link' => 'https://www.instagram.com/gpdigadingserpong',
                    ],
                    [
                        'title' => 'Lokasi',
                        'desc' => 'Kompleks Pasar Modern Paramount Blok L39, Gading Serpong',
                        'link' => 'https://maps.app.goo.gl/GoMhnkkmL2A7wp6c9',
                    ],
                ],
            ],
            'Ministries Page' => [
                'meta' => [
                    'title' => 'Pelayanan — Hebron Family Church',
                    'description' => 'Temukan tempatmu melayani di Hebron Family Church: Praise & Worship, Production, Service & Hospitality, Creative Production, dan Prayer.',
                    'image' => 'ministry-page-hero/01K4W79WCYCMJQT45MJ6S2SPX4.jpg',
                ],
                'hero' => [
                    'title' => 'Ministry at Hebron Family Church',
                    'image' => 'ministry-page-hero/01K4W79WCYCMJQT45MJ6S2SPX4.jpg',
                ],
                'intro' => [
                    'title' => 'Mari Melayani Tuhan Bersama',
                    'desc' => '<p>Pelayanan adalah ekspresi kasih dan ucapan syukur jemaat Tuhan dalam pengalaman, pertumbuhan kedewasaan rohani di dalam Tuhan. Melalui beragam wadah pelayanan yang ada pada Hebron Family Church, kami percaya setiap jemaat dapat memuliakan Tuhan dengan talenta yang mereka miliki dan kesempatan yang Tuhan berikan.</p>',
                ],
                'serve' => [
                    'title' => 'Our Ministry',
                    'ministryList' => [
                        [
                            'title' => 'Creative Production',
                            'desc' => '<p>- Event Planner<br>- Event Coordinator</p>',
                            'image' => 'ministry-page-ministries/01K4W79WD4G28VGCCT37H0GAV2.jpg',
                            'link' => 'https://forms.gle/8Hc5VSgDXeU9NYB86',
                        ],
                        [
                            'title' => 'Praise & Worship',
                            'desc' => '<p>- Vocal</p><p>- Music</p><p>- Sound Manager</p>',
                            'image' => 'ministry-page-ministries/01K47E3AGH5FGM98M0J1FHAJEF.jpg',
                            'link' => 'https://forms.gle/8Hc5VSgDXeU9NYB86',
                        ],
                        [
                            'title' => 'Service & Hospitality',
                            'desc' => '<p>- Offering Preacher&nbsp;<br>- Holy Communion</p><p>- Usher &amp; Hospitality&nbsp;<br>- Stage Manager&nbsp;</p>',
                            'image' => 'ministry-page-ministries/01K4W79WDADP6DCSGQFE0X31X6.jpg',
                            'link' => 'https://forms.gle/8Hc5VSgDXeU9NYB86',
                        ],
                        [
                            'title' => 'Production',
                            'desc' => '<p>- Multimedia &amp; Live Streaming&nbsp;<br>- Design &amp; Editing&nbsp;<br>- Website &amp; Admin&nbsp;<br>- Sosial Media&nbsp;<br>- Photographer &amp; Documentation</p>',
                            'image' => 'ministry-page-ministries/01K4W79WDE7RXVC98X17X2AR97.jpg',
                            'link' => 'https://forms.gle/8Hc5VSgDXeU9NYB86',
                        ],
                        [
                            'title' => 'Prayer',
                            'desc' => '<p>- Friday Prayer Service<br>- Doa Sepekan</p>',
                            'image' => 'ministry-page-ministries/01K47EM32TT82962DW90NTRJRM.jpg',
                            'link' => 'https://forms.gle/8Hc5VSgDXeU9NYB86',
                        ],
                    ],
                ],
                'faq' => [
                    'title' => 'Bergabung bersama kami',
                    'questionsList' => [
                        [
                            'question' => 'Siapa saja yang bisa bergabung melayani?',
                            'answer' => '<p>Setiap jemaat Hebron Family Church yang sudah lahir baru dan rindu melayani Tuhan dapat bergabung. Tidak perlu berpengalaman — kami akan memperlengkapi Anda.</p>',
                        ],
                        [
                            'question' => 'Bagaimana cara mendaftar?',
                            'answer' => '<p>Pilih pelayanan yang Anda minati lalu isi formulir pendaftaran. Koordinator pelayanan akan menghubungi Anda untuk langkah selanjutnya.</p>',
                        ],
                        [
                            'question' => 'Apakah ada pelatihan untuk pelayan baru?',
                            'answer' => '<p>Ya. Setiap pelayanan memiliki masa orientasi dan latihan rutin agar Anda siap melayani dengan baik.</p>',
                        ],
                        [
                            'question' => 'Berapa lama komitmen melayani?',
                            'answer' => '<p>Jadwal pelayanan diatur bergiliran sesuai tim. Kami mengajak setiap pelayan berkomitmen minimal satu tahun agar dapat bertumbuh bersama tim.</p>',
                        ],
                    ],
                ],
            ],
            'Division Page' => [
                'meta' => [
                    'title' => 'Divisi — Hebron Family Church',
                    'description' => 'Ibadah untuk setiap generasi: SoT untuk anak, DoT\'s untuk remaja, dan SPoT untuk mahasiswa & young professional.',
                    'image' => 'divisions-image/01K42K1GAGYD9CE92R13EVBWEM.jpg',
                ],
                'hero' => [
                    'title' => 'Ibadah untuk setiap generasi',
                    'image' => 'divisions-image/01K42JNDFT12PC0JCV349PXMW6.jpg',
                ],
            ],
        ];
    }

    private function devotions(): array
    {
        return [
            [
                'title' => 'Satu Keluarga di dalam Kristus',
                'slug' => 'satu-keluarga-di-dalam-kristus',
                'category' => 'devotion',
                'image' => 'home-page-carousel/01K41T4APAHCPJPSE1XDGPDSY9.jpg',
                'published_at' => '2026-09-06',
                'content' => '<p><strong>Efesus 2:19</strong> — "Demikianlah kamu bukan lagi orang asing dan pendatang, melainkan kawan sewarga dari orang-orang kudus dan anggota-anggota keluarga Allah."</p><p>Di dalam Kristus, kita tidak dipanggil untuk berjalan sendiri. Allah menempatkan kita di dalam sebuah keluarga — gereja — tempat kita saling menguatkan, saling menegur dalam kasih, dan bertumbuh bersama.</p><p>Keluarga rohani bukan tentang kesempurnaan, melainkan tentang kesetiaan untuk tetap hadir satu bagi yang lain. Minggu ini, sapalah seseorang yang belum Anda kenal di ibadah, dan doakan satu anggota keluarga rohani Anda secara khusus.</p><p><em>Doa: Tuhan, terima kasih karena Engkau menjadikan aku bagian dari keluarga-Mu. Ajar aku mengasihi saudara-saudaraku seperti Engkau mengasihiku. Amin.</em></p>',
            ],
            [
                'title' => 'Hidup Seperti Kristus dalam Hal-hal Kecil',
                'slug' => 'hidup-seperti-kristus-dalam-hal-hal-kecil',
                'category' => 'devotion',
                'image' => 'home-page-carousel/01K41T4APD74AFJNN9AZ2CCEWV.jpg',
                'published_at' => '2026-09-13',
                'content' => '<p><strong>Lukas 16:10</strong> — "Barangsiapa setia dalam perkara-perkara kecil, ia setia juga dalam perkara-perkara besar."</p><p>Serupa dengan Kristus sering kali tidak terlihat dari hal-hal besar, tetapi dari keputusan kecil setiap hari: menjaga perkataan, menepati janji, dan bersikap jujur ketika tidak ada yang melihat.</p><p>Karakter dibentuk sedikit demi sedikit. Setiap kali kita memilih taat dalam perkara kecil, Roh Kudus sedang membentuk kita menjadi pribadi yang dapat dipercaya untuk perkara yang lebih besar.</p><p><em>Renungkan: Hal kecil apa yang Tuhan ingin Anda setiai minggu ini?</em></p>',
            ],
            [
                'title' => 'Pendaftaran Pelayan Baru Dibuka',
                'slug' => 'pendaftaran-pelayan-baru-dibuka',
                'category' => 'announcement',
                'image' => 'ministry-page-hero/01K4W79WCYCMJQT45MJ6S2SPX4.jpg',
                'published_at' => '2026-09-20',
                'content' => '<p>Hebron Family Church membuka kesempatan bagi jemaat yang rindu melayani di Praise & Worship, Production, Service & Hospitality, Creative Production, dan Prayer.</p><p>Silakan lihat daftar pelayanan di halaman <a href="/ministry">Pelayanan</a> dan isi formulir pendaftaran. Koordinator pelayanan akan menghubungi Anda untuk jadwal orientasi.</p><p>Mari melayani Tuhan bersama!</p>',
            ],
        ];
    }
}
