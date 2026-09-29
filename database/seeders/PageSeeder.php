<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Pages;
use App\Models\Pastor;
use Illuminate\Database\Seeder;

/**
 * Page content snapshot of the live site (https://gpdi-hebron.com).
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
                'description' => 'College & Young Pro Community',
                'image' => 'divisions-image/01K42K1GAGYD9CE92R13EVBWEM.jpg',
                'time' => 'Saturday, on 4.30 pm',
                'link' => 'https://www.instagram.com/spot_gen?igsh=ZzJ6bTIya3J1YWdx',
                'sort' => 0,
            ],
            [
                'title' => 'DoT\'s',
                'description' => 'Descendants Of Truth\'s',
                'image' => 'divisions-image/01K42JNDFT12PC0JCV349PXMW6.jpg',
                'time' => 'Saturday, on 2.00 pm',
                'link' => 'https://www.instagram.com/dots_gen?igsh=bGF1NXAzNXZtbGpt',
                'sort' => 1,
            ],
            [
                'title' => 'SoT',
                'description' => 'Seeds Of Truth',
                'image' => 'divisions-image/01K42K067AXAH5CFN7MMQTDFTY.jpg',
                'time' => 'Sunday, 10.00 am',
                'link' => 'https://www.instagram.com/seedsoftruth__?igsh=azlwa3FkNm1oMHd2',
                'sort' => 2,
            ],
        ] as $division) {
            Division::updateOrCreate(['title' => $division['title']], $division);
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
                    'social_media' => [
                        [
                            'platform' => 'Instagram',
                            'url' => 'https://www.instagram.com/gpdigadingserpong',
                        ],
                    ],
                ],
            ],
            'Home' => [
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
                            'time' => '07.30 WIB',
                        ],
                        [
                            'title' => 'Ibadah Raya 2',
                            'time' => '10.00 WIB',
                        ],
                        [
                            'title' => 'Ibadah Raya 3',
                            'time' => '17.30 WIB',
                        ],
                    ],
                    'division_button_text' => 'Lihat Ibadah Divisi Hebron',
                ],
                'pastor_greeting' => [
                    'subtitle' => 'Sambutan dari Gembala Kami',
                    'pastors_name' => 'Pdt. Raimond Sumaa & Pdt. Yesie Sumaa',
                    'pastor_img' => 'home-page-pastor/01K4CWD64XS1BNK1TQGB0Z8QSP.jpg',
                    'pastor_signature' => 'home-page-pastor/01K4CWGHWCJ87GAEHWMXMCTXFS.png',
                    'desc' => '<p>"United as One Family, to Live Like christ"</p>',
                    'pastoral_button_text' => 'Lihat tim Pastoral Hebron',
                ],
            ],
            'About' => [
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
                        'icon' => 'a',
                        'title' => 'Holiness',
                        'desc' => '<p><strong>Ibrani 12:4</strong></p><p>Deep in words of God<br>Deep in the Holy Spirit<br>Deep in worship and prayer<br><br></p>',
                    ],
                    [
                        'icon' => 'a',
                        'title' => 'Obedience',
                        'desc' => '<p><strong>Yohanes 14:23</strong></p><p>Grow in character and attitude<br>Grow in leadership (authority)<br>Grow in commitment and giving (keteladanan)<br><br><br></p>',
                    ],
                    [
                        'icon' => 'a',
                        'title' => 'Ministry',
                        'desc' => '<p><strong>Roma 12:11</strong></p><p>Serve families<br>Serve communities and others<br>Serve with enthusiasm<br><br></p>',
                    ],
                    [
                        'icon' => 'a',
                        'title' => 'Evangelism',
                        'desc' => '<p><strong>Kolose 3:14</strong></p><p>Serve families<br>Serve communities and others<br>Serve with enthusiasm<br><br></p>',
                    ],
                ],
                'history' => [
                    [
                        'title' => 'Sejarah Hebron',
                        'desc' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec varius pulvinar ultrices. Phasellus ornare molestie massa, non lacinia quam. Proin tempus tempus libero ultrices facilisis. Phasellus venenatis ex non velit ultrices, non consectetur nisl lacinia.&nbsp;</p>',
                        'date' => '2006',
                        'image' => 'about-page-history/01K21G3YWDXNCNX7YCX36G2A5C.jpg',
                    ],
                ],
            ],
            'Contact' => [
                'hero' => [
                    'title' => 'Contact Us',
                    'subtitle' => '....',
                    'image' => 'contact-page-hero/01K4W9SA4F13GQ49VW1QWT9Z33.jpg',
                ],
                'contact_list' => [
                    [
                        'title' => 'Sekretariat',
                        'desc' => '081387243736/082194200174',
                        'link' => null,
                    ],
                ],
            ],
            'Ministries Page' => [
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
                    'questionsList' => [],
                ],
            ],
            'Division Page' => [
                'hero' => [],
            ],
        ];
    }
}
