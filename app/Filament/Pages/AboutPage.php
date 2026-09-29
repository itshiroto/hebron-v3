<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\HasSeoMeta;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use App\Models\Pages;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\HtmlString;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class AboutPage extends Page implements HasForms
{
    use InteractsWithForms, HasSeoMeta;

    public ?array $metaData = [];

    // protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected string $view = 'filament.pages.about-page';

    protected static ?string $title = 'About Us';
    protected static string | \UnitEnum | null $navigationGroup = 'Pages';
    protected static ?int $navigationSort = 1;

    public ?array $heroData = [];
    public ?array $visionMissionData = [];
    public ?array $valuesData = [];
    public ?array $beliefsData = [];

    protected $aboutPage;

    public function mount(): void
    {
        $this->aboutPage = Pages::firstOrCreate(['name' => 'About']);

        $heroSection = $this->aboutPage->sections()->firstOrCreate(
            ['name' => 'hero', 'page_id' => $this->aboutPage->id],
            ['content' => []]
        );
        $this->heroData = $heroSection->content;

        $visionMissionSection = $this->aboutPage->sections()->firstOrCreate(
            ['name' => 'vision_mission', 'page_id' => $this->aboutPage->id],
            ['content' => []]
        );
        $this->visionMissionData = $visionMissionSection->content;

        $valuesSection = $this->aboutPage->sections()->firstOrCreate(
            ['name' => 'values', 'page_id' => $this->aboutPage->id],
            ['content' => []]
        );
        $this->valuesData = $valuesSection->content;

        $beliefsSection = $this->aboutPage->sections()->firstOrCreate(
            ['name' => 'beliefs', 'page_id' => $this->aboutPage->id],
            ['content' => []]
        );
        $this->beliefsData = $beliefsSection->content;

        // Ensure the form is filled with data from the properties
        $this->form->fill([
            'metaData' => $this->loadSeoMeta($this->aboutPage),
            'heroData' => $this->heroData,
            'visionMissionData' => $this->visionMissionData,
            'valuesData' => $this->valuesData,
            'beliefsData' => $this->beliefsData,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Hero Section')
                ->description('Configure the main hero section of the About page.')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('heroData.title')
                            ->label('Title')
                            ->placeholder('Tentang Hebron'),
                        TextInput::make('heroData.subtitle')
                            ->label('Subtitle'),
                    ]),
                    FileUpload::make('heroData.image')
                        ->label('Background Image')
                        ->image()
                        ->imageEditor()
                        ->directory('about-page-hero')
                        ->required()
                ]),

            Section::make('Vision and Mission Section')
                ->description('Define the vision and mission statements.')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('visionMissionData.visi.title')
                            ->label('Vision Title')
                            ->required(),
                        TextInput::make('visionMissionData.misi.title')
                            ->label('Mission Title')
                            ->required(),
                    ]),
                    Grid::make(2)->schema([
                        RichEditor::make('visionMissionData.visi.desc')
                            ->label('Vision Description')
                            ->required(),
                        RichEditor::make('visionMissionData.misi.desc')
                            ->label('Mission Description')
                            ->required(),
                    ]),
                    FileUpload::make('visionMissionData.image')
                        ->label('Image')
                        ->image()
                        ->imageEditor()
                        ->directory('about-page-vision-mission')
                        ->required()
                ]),

            Section::make('Values Section')
                ->description('List the core values of your organization.')
                ->schema([
                    Repeater::make('valuesData')
                        ->label('Values')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('title')
                                    ->label('Title')
                                    ->required(),
                                TextInput::make('icon')
                                    ->label('Icon')
                                    ->placeholder('lucide:flame')
                                    ->hint(new HtmlString('Nama Iconnya bisa diambil di <a class="underline" href="https://icones.js.org/collection/all">icones.js.org</a>')),
                            ]),
                            Grid::make(1)->schema([
                                RichEditor::make('desc')
                                    ->label('Description')
                                    ->required(),
                            ])
                        ])
                        ->defaultItems(1)
                        ->columns(3)
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                ]),

            Section::make('Beliefs Section (Pengakuan Iman)')
                ->description('Ditampilkan di halaman Tentang Kami.')
                ->schema([
                    TextInput::make('beliefsData.title')
                        ->label('Title')
                        ->placeholder('Apa yang kami percaya'),
                    RichEditor::make('beliefsData.intro')
                        ->label('Intro'),
                    TextInput::make('beliefsData.link')
                        ->label('Link to full Pengakuan Iman GPdI')
                        ->url(),
                    Repeater::make('beliefsData.items')
                        ->label('Belief items')
                        ->schema([
                            TextInput::make('title')->label('Title')->required(),
                            Textarea::make('desc')->label('One-sentence summary')->rows(2)->required(),
                            TextInput::make('verse')->label('Verse reference'),
                        ])
                        ->columns(1)
                        ->reorderable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                ]),
            $this->seoSection(),
        ]);
    }

    public function submit(): void
    {
        $this->aboutPage = Pages::firstOrCreate(['name' => 'About']);

        $data = $this->form->getState();

        // Handle image paths for heroData
        if (isset($data['heroData']['image']) && is_array($data['heroData']['image'])) {
            $data['heroData']['image'] = $data['heroData']['image'][0] ?? null;
        }

        // Handle image paths for visionMissionData
        if (isset($data['visionMissionData']['image']) && is_array($data['visionMissionData']['image'])) {
            $data['visionMissionData']['image'] = $data['visionMissionData']['image'][0] ?? null;
        }

        $this->aboutPage->sections()->updateOrCreate(
            ['name' => 'hero', 'page_id' => $this->aboutPage->id],
            ['content' => $data['heroData']]
        );

        $this->aboutPage->sections()->updateOrCreate(
            ['name' => 'vision_mission', 'page_id' => $this->aboutPage->id],
            ['content' => $data['visionMissionData']]
        );

        $this->aboutPage->sections()->updateOrCreate(
            ['name' => 'values', 'page_id' => $this->aboutPage->id],
            ['content' => $data['valuesData']]
        );

        $this->aboutPage->sections()->updateOrCreate(
            ['name' => 'beliefs', 'page_id' => $this->aboutPage->id],
            ['content' => $data['beliefsData'] ?? []]
        );

        $this->saveSeoMeta($this->aboutPage, $data['metaData'] ?? []);

        Notification::make()
            ->title('About page content updated successfully!')
            ->success()
            ->send();
    }
}
