<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use App\Models\Pages;

class HomePage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.home-page';

    protected static ?string $title = 'Home';
    protected static ?string $navigationGroup = 'Pages';

    public ?array $heroData = [];
    public ?array $visionMissionData = [];
    public ?array $ministriesData = [];

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

        $ministriesSection = $this->homePage->sections()->firstOrCreate(
            ['name' => 'values', 'page_id' => $this->homePage->id],
            ['content' => []]
        );
        $this->ministriesData = $ministriesSection->content;

        $this->form->fill([
            'heroData' => $this->heroData,
            'visionMissionData' => $this->visionMissionData,
            'ministriesData' => $this->ministriesData,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
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
                    Grid::make(2)->schema([
                        TextInput::make('heroData.button_text1')
                            ->label('Button Text 1'),
                        TextInput::make('heroData.button_text2')
                            ->label('Button Text 2'),
                    ]),
                ]),

            Section::make('Vision and Mission Section')
                ->description('Define the vision and mission statements.')
                ->schema([
                    TextInput::make('visionMissionData.title')
                        ->label('Title')
                        ->required(),
                    RichEditor::make('visionMissionData.visi')
                        ->label('Vision Description')
                        ->required(),
                    RichEditor::make('visionMissionData.misi')
                        ->label('Mission Description')
                        ->required(),
                    FileUpload::make('visionMissionData.image')
                        ->label('Carousel Image')
                        ->image()
                        ->multiple()
                        ->imageEditor()
                        ->directory('home-page-vision-mission')
                        ->required()
                ]),

            Section::make('Ministries Section')
                ->description('Define the ministries of your organization.')
                ->schema([
                    Repeater::make('ministriesData')
                        ->label('Values')
                        ->schema([


                            Grid::make(2)->schema([
                                TextInput::make('title')
                                    ->label('Title')
                                    ->required(),
                                TextInput::make('hours')
                                    ->label('hours')
                                    ->required(),
                            ]),

                            Grid::make(1)->schema([
                                FileUpload::make('visionMissionData.image')
                                    ->label('Image')
                                    ->image()
                                    ->imageEditor()
                                    ->directory('home-page-vision-mission')
                                    ->required(),
                            ]),
                            Grid::make(1)->schema([
                                RichEditor::make('desc')
                                    ->label('Description')
                                    ->required(),
                            ]),
                        ])
                        ->defaultItems(1)
                        ->columns(3)
                        ->collapsible()
                        ->itemLabel(fn(array $state): ?string => $state['title'] ?? null),
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

        // Handle image paths for visionMissionData
        if (isset($data['visionMissionData']['image']) && is_array($data['visionMissionData']['image'])) {
            $data['visionMissionData']['image'] = $data['visionMissionData']['image'][0] ?? null;
        }

        // Handle image paths for ministriesData (within repeater)
        if (isset($data['ministriesData']) && is_array($data['ministriesData'])) {
            foreach ($data['ministriesData'] as $key => $item) {
                if (isset($item['image']) && is_array($item['image'])) {
                    $data['ministriesData'][$key]['image'] = $item['image'][0] ?? null;
                }
            }
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
            ['name' => 'values', 'page_id' => $this->homePage->id],
            ['content' => $data['ministriesData']]
        );

        Notification::make()
            ->title('Home page content updated successfully!')
            ->success()
            ->send();
    }
}
