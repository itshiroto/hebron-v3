<?php

namespace App\Filament\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Pages\Page;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use App\Models\Pages;

class HomePage extends Page implements HasForms
{
    use InteractsWithForms;

    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.pages.home-page';

    protected static ?string $title = 'Home';
    protected static string | \UnitEnum | null $navigationGroup = 'Pages';
    protected static ?int $navigationSort = 0;

    public ?array $heroData = [];
    public ?array $visionMissionData = [];
    public ?array $carouselData = [];
    public ?array $scheduleData = [];
    public ?array $pastorGreetingData = [];

    protected $homePage;

    public function mount(): void
    {
        $this->homePage = Pages::firstOrCreate(['name' => 'Home']);

        $heroSection = $this->homePage->sections()->firstOrCreate(
            ['name' => 'hero', 'page_id' => $this->homePage->id],
            ['content' => []]
        );
        $this->heroData = $heroSection->content;

        $visionMissionSection = $this->homePage->sections()->firstOrCreate(
            ['name' => 'vision_mission', 'page_id' => $this->homePage->id],
            ['content' => []]
        );
        $this->visionMissionData = $visionMissionSection->content;

        $carouselSection = $this->homePage->sections()->firstOrCreate(
            ['name' => 'carousel', 'page_id' => $this->homePage->id],
            ['content' => []]
        );
        $this->carouselData = $carouselSection->content;

        $scheduleSection = $this->homePage->sections()->firstOrCreate(
            ['name' => 'schedule', 'page_id' => $this->homePage->id],
            ['content' => []]
        );
        $this->scheduleData = $scheduleSection->content;

        $pastorGreetingSection = $this->homePage->sections()->firstOrCreate(
            ['name' => 'pastor_greeting', 'page_id' => $this->homePage->id],
            ['content' => []]
        );
        $this->pastorGreetingData = $pastorGreetingSection->content;


        $this->form->fill([
            'heroData' => $this->heroData,
            'visionMissionData' => $this->visionMissionData,
            'carouselData' => $this->carouselData,
            'scheduleData' => $this->scheduleData,
            'pastorGreetingData' => $this->pastorGreetingData,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Hero Section')
                ->description('Configure the main hero section of the Home page.')
                ->schema([
                    TextInput::make('heroData.title')
                        ->label('Title')
                        ->required(),
                    FileUpload::make('heroData.image')
                        ->label('Background Image')
                        ->image()
                        ->imageEditor()
                        ->directory('home-page-hero')
                        ->required(),
                    FileUpload::make('heroData.video')
                        ->label('Background Video (optional, muted loop)')
                        ->acceptedFileTypes(['video/mp4', 'video/webm'])
                        // Livewire's default temporary-upload rule caps files at 12 MB.
                        ->maxSize(12288)
                        ->directory('home-page-hero')
                        ->helperText('Maks. 12 MB. Gambar latar dipakai sebagai poster dan cadangan.'),
                    Grid::make(2)->schema([
                        TextInput::make('heroData.primary_button.text')
                            ->label('Primary Button Text'),
                        TextInput::make('heroData.primary_button.link')
                            ->label('Primary Button URL'),
                        TextInput::make('heroData.secondary_button.text')
                            ->label('Secondary Button Text'),
                        TextInput::make('heroData.secondary_button.link')
                            ->label('Secondary Button URL'),
                    ]),
                ]),

            Section::make('Vision and Mission Section')
                ->description('Define the vision and mission statements.')
                ->schema([
                    TextInput::make('visionMissionData.title')
                        ->label('Title')
                        ->required(),
                    TextInput::make('visionMissionData.visi.title')
                        ->label('Vision Title')
                        ->required(),
                    RichEditor::make('visionMissionData.visi.desc')
                        ->label('Vision Description')
                        ->required(),
                    TextInput::make('visionMissionData.misi.title')
                        ->label('Mission Title')
                        ->required(),
                    RichEditor::make('visionMissionData.misi.desc')
                        ->label('Mission Description')
                        ->required(),
                    TextInput::make('visionMissionData.about_button_text')
                        ->label('About Button Text')
                        ->required(),
                ]),

            Section::make('Carousel Section')
                ->description('Upload images for the carousel.')
                ->schema([
                    FileUpload::make('carouselData')
                        ->label('Carousel Images')
                        ->image()
                        ->multiple()
                        ->imageEditor()
                        ->directory('home-page-carousel')
                        ->required()
                ]),

            Section::make('Schedule Section')
                ->description('Configure the service schedule.')
                ->schema([
                    TextInput::make('scheduleData.title')
                        ->label('Title')
                        ->required(),
                    TextInput::make('scheduleData.subtitle')
                        ->label('Subtitle')
                        ->required(),
                    FileUpload::make('scheduleData.img')
                        ->label('Background Image')
                        ->image()
                        ->imageEditor()
                        ->directory('home-page-schedule')
                        ->required(),
                    Repeater::make('scheduleData.services')
                        ->label('Services')
                        ->schema([
                            TextInput::make('title')
                                ->label('Service Title')
                                ->required(),
                            TextInput::make('time')
                                ->label('Service Time')
                                ->required(),
                            Toggle::make('livestream')
                                ->label('Live Streaming')
                                ->default(false),
                        ])
                        ->defaultItems(1)
                        ->columns(3)
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                    TextInput::make('scheduleData.division_button_text')
                        ->label('Division Button Text')
                        ->required(),
                ]),

            Section::make('Pastor Greeting Section')
                ->description('Configure the pastor\'s greeting section.')
                ->schema([
                    TextInput::make('pastorGreetingData.subtitle')
                        ->label('Subtitle')
                        ->required(),
                    TextInput::make('pastorGreetingData.pastors_name')
                        ->label('Pastor\'s Name')
                        ->required(),
                    FileUpload::make('pastorGreetingData.pastor_img')
                        ->label('Pastor Image')
                        ->image()
                        ->imageEditor()
                        ->directory('home-page-pastor')
                        ->required(),
                    FileUpload::make('pastorGreetingData.pastor_signature')
                        ->label('Pastor Signature Image')
                        ->image()
                        ->imageEditor()
                        ->directory('home-page-pastor')
                        ->required(),
                    RichEditor::make('pastorGreetingData.desc')
                        ->label('Description')
                        ->required(),
                    TextInput::make('pastorGreetingData.pastoral_button_text')
                        ->label('Pastoral Button Text')
                        ->required(),
                ]),
        ]);
    }

    public function submit(): void
    {
        $this->homePage = Pages::firstOrCreate(['name' => 'Home']);

        $data = $this->form->getState();

        // Handle image paths for heroData
        if (isset($data['heroData']['image']) && is_array($data['heroData']['image'])) {
            $data['heroData']['image'] = $data['heroData']['image'][0] ?? null;
        }

        if (isset($data['heroData']['video']) && is_array($data['heroData']['video'])) {
            $data['heroData']['video'] = $data['heroData']['video'][0] ?? null;
        }

        // Handle image paths for carouselData
        if (isset($data['carouselData']) && is_array($data['carouselData'])) {
            $data['carouselData'] = array_values($data['carouselData']); // Reindex array
        }

        // Handle image paths for scheduleData
        if (isset($data['scheduleData']['img']) && is_array($data['scheduleData']['img'])) {
            $data['scheduleData']['img'] = $data['scheduleData']['img'][0] ?? null;
        }

        // Handle image paths for pastorGreetingData
        if (isset($data['pastorGreetingData']['pastor_img']) && is_array($data['pastorGreetingData']['pastor_img'])) {
            $data['pastorGreetingData']['pastor_img'] = $data['pastorGreetingData']['pastor_img'][0] ?? null;
        }
        if (isset($data['pastorGreetingData']['pastor_signature']) && is_array($data['pastorGreetingData']['pastor_signature'])) {
            $data['pastorGreetingData']['pastor_signature'] = $data['pastorGreetingData']['pastor_signature'][0] ?? null;
        }


        $this->homePage->sections()->updateOrCreate(
            ['name' => 'hero', 'page_id' => $this->homePage->id],
            ['content' => $data['heroData']]
        );

        $this->homePage->sections()->updateOrCreate(
            ['name' => 'vision_mission', 'page_id' => $this->homePage->id],
            ['content' => $data['visionMissionData']]
        );

        $this->homePage->sections()->updateOrCreate(
            ['name' => 'carousel', 'page_id' => $this->homePage->id],
            ['content' => $data['carouselData']]
        );

        $this->homePage->sections()->updateOrCreate(
            ['name' => 'schedule', 'page_id' => $this->homePage->id],
            ['content' => $data['scheduleData']]
        );

        $this->homePage->sections()->updateOrCreate(
            ['name' => 'pastor_greeting', 'page_id' => $this->homePage->id],
            ['content' => $data['pastorGreetingData']]
        );

        Notification::make()
            ->title('Home page content updated successfully!')
            ->success()
            ->send();
    }
}
