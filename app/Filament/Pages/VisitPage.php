<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\HasSeoMeta;
use App\Models\Division;
use App\Models\Pages;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VisitPage extends Page implements HasForms
{
    use InteractsWithForms, HasSeoMeta;

    private const SECTIONS = ['hero', 'expect', 'arrival', 'kids', 'welcome', 'faq'];

    protected string $view = 'filament.pages.visit-page';

    protected static ?string $title = 'Kunjungi Kami';
    protected static string | \UnitEnum | null $navigationGroup = 'Pages';
    protected static ?int $navigationSort = 5;

    public ?array $metaData = [];
    public ?array $heroData = [];
    public ?array $expectData = [];
    public ?array $arrivalData = [];
    public ?array $kidsData = [];
    public ?array $welcomeData = [];
    public ?array $faqData = [];

    public function mount(): void
    {
        $page = Pages::firstOrCreate(['name' => 'Visit']);

        $fill = ['metaData' => $this->loadSeoMeta($page)];
        foreach (self::SECTIONS as $name) {
            $fill["{$name}Data"] = $page->sections()->firstOrCreate(
                ['name' => $name, 'page_id' => $page->id],
                ['content' => []]
            )->content;
        }

        $this->form->fill($fill);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Hero Section')
                ->description('Service times under the hero come from Home › Schedule.')
                ->schema([
                    TextInput::make('heroData.title')
                        ->label('Title')
                        ->placeholder('Minggu ini, ada tempat untukmu'),
                    TextInput::make('heroData.subtitle')
                        ->label('Subtitle'),
                    FileUpload::make('heroData.image')
                        ->label('Background Image')
                        ->image()
                        ->imageEditor()
                        ->directory('visit-page-hero'),
                ]),

            Section::make('Apa yang akan kamu alami')
                ->description('Short facts about a Sunday service: duration, flow, Kantin Hebron, etc. Hidden on the site for now.')
                ->schema([
                    TextInput::make('expectData.title')
                        ->label('Title')
                        ->placeholder('Apa yang akan kamu alami'),
                    Repeater::make('expectData.items')
                        ->label('Items')
                        ->schema([
                            TextInput::make('title')->label('Title')->required(),
                            Textarea::make('desc')->label('Description')->rows(2)->required(),
                        ])
                        ->defaultItems(1)
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                ]),

            Section::make('Cara sampai')
                ->description('Directions to the church. Address and Google Maps link come from General Settings.')
                ->schema([
                    TextInput::make('arrivalData.title')
                        ->label('Title')
                        ->placeholder('Cara sampai ke Hebron'),
                    Textarea::make('arrivalData.parking')
                        ->label('Parking')
                        ->rows(2),
                    Repeater::make('arrivalData.steps')
                        ->label('Steps')
                        ->schema([
                            TextInput::make('title')->label('Title')->required(),
                            Textarea::make('desc')->label('Description')->rows(2),
                            FileUpload::make('image')
                                ->label('Photo (optional)')
                                ->image()
                                ->imageEditor()
                                ->directory('visit-page-arrival'),
                        ])
                        ->defaultItems(1)
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                ]),

            Section::make('Anak & remaja')
                ->schema([
                    TextInput::make('kidsData.title')
                        ->label('Title')
                        ->placeholder('Bawa anak & remaja'),
                    Textarea::make('kidsData.desc')
                        ->label('Description')
                        ->rows(3),
                    Select::make('kidsData.division_ids')
                        ->label('Divisions to show')
                        ->multiple()
                        ->options(fn () => Division::ordered()->pluck('title', 'id')),
                ]),

            Section::make('Kabari Kami')
                ->description('The "let us know you are coming" button, e.g. a WhatsApp link to the welcome team.')
                ->schema([
                    TextInput::make('welcomeData.title')
                        ->label('Title')
                        ->placeholder('Saat kamu tiba'),
                    Textarea::make('welcomeData.desc')
                        ->label('Description')
                        ->rows(3),
                    TextInput::make('welcomeData.button_text')
                        ->label('Button Text')
                        ->placeholder('Kabari kami'),
                    TextInput::make('welcomeData.button_link')
                        ->label('Button URL')
                        ->url()
                        ->helperText('Contoh: https://wa.me/6281234567890?text=Shalom%2C%20saya%20akan%20datang. Kosongkan untuk menyembunyikan tombol.'),
                ]),

            Section::make('FAQ Section')
                ->schema([
                    TextInput::make('faqData.title')
                        ->label('Title')
                        ->placeholder('Pertanyaan yang sering diajukan'),
                    Repeater::make('faqData.questionsList')
                        ->label('Questions and Answers')
                        ->schema([
                            TextInput::make('question')->label('Question')->required(),
                            RichEditor::make('answer')->label('Answer')->required(),
                        ])
                        ->defaultItems(1)
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['question'] ?? null),
                ]),
            $this->seoSection(),
        ]);
    }

    public function submit(): void
    {
        $page = Pages::firstOrCreate(['name' => 'Visit']);
        $data = $this->form->getState();

        if (isset($data['heroData']['image']) && is_array($data['heroData']['image'])) {
            $data['heroData']['image'] = $data['heroData']['image'][0] ?? null;
        }
        foreach ($data['arrivalData']['steps'] ?? [] as $key => $step) {
            if (isset($step['image']) && is_array($step['image'])) {
                $data['arrivalData']['steps'][$key]['image'] = $step['image'][0] ?? null;
            }
        }

        foreach (self::SECTIONS as $name) {
            $page->sections()->updateOrCreate(
                ['name' => $name, 'page_id' => $page->id],
                ['content' => $data["{$name}Data"] ?? []]
            );
        }

        $this->saveSeoMeta($page, $data['metaData'] ?? []);

        Notification::make()
            ->title('Kunjungi Kami page updated successfully!')
            ->success()
            ->send();
    }
}
