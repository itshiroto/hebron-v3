<?php

namespace App\Filament\Pages;

use App\Models\Pages;
use App\Models\Sections;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form; // Make sure this is imported
use Filament\Pages\Page;
use Illuminate\Support\Str;

class AboutPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.about-page';

    protected static ?string $title = 'About Page';

    public ?array $heroData = [];
    public ?array $visionMissionData = [];
    public ?array $valuesData = [];
    public ?array $historyData = [];

    // Remove the type declaration here to allow late initialization,
    // or ensure it's always initialized. Re-initializing in submit() is safer.
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

        $historySection = $this->aboutPage->sections()->firstOrCreate(
            ['name' => 'history', 'page_id' => $this->aboutPage->id],
            ['content' => []]
        );
        $this->historyData = $historySection->content;

        // Ensure the form is filled with data from the properties
        $this->form->fill([
            'heroData' => $this->heroData,
            'visionMissionData' => $this->visionMissionData,
            'valuesData' => $this->valuesData,
            'historyData' => $this->historyData,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Hero Section')
                ->description('Configure the main hero section of the About page.')
                ->schema([
                    TextInput::make('heroData.title')
                        ->label('Title')
                        ->required(),
                    TextInput::make('heroData.subtitle')
                        ->label('Subtitle')
                        ->required(),
                    FileUpload::make('heroData.image')
                        ->label('Background Image')
                        ->image()
                        ->directory('about-page-hero')
                        ->required(),
                ]),

            Section::make('Vision and Mission Section')
                ->description('Define the vision and mission statements.')
                ->schema([
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
                    FileUpload::make('visionMissionData.image')
                        ->label('Image')
                        ->image()
                        ->directory('about-page-vision-mission')
                        ->required(),
                ]),

            Section::make('Values Section')
                ->description('List the core values of your organization.')
                ->schema([
                    Repeater::make('valuesData')
                        ->label('Values')
                        ->schema([
                            TextInput::make('icon')
                                ->label('Icon (e.g., Font Awesome class)')
                                ->required(),
                            TextInput::make('title')
                                ->label('Title')
                                ->required(),
                            RichEditor::make('desc')
                                ->label('Description')
                                ->required(),
                        ])
                        ->defaultItems(1)
                        ->columns(3)
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                ]),

            Section::make('History Section')
                ->description('Chronicle the history of your organization.')
                ->schema([
                    Repeater::make('historyData')
                        ->label('History Events')
                        ->schema([
                            TextInput::make('title')
                                ->label('Event Title')
                                ->required(),
                            RichEditor::make('desc')
                                ->label('Event Description')
                                ->required(),
                            TextInput::make('date')
                                ->label('Date (e.g., "2023")')
                                ->required(),
                            FileUpload::make('image')
                                ->label('Image')
                                ->image()
                                ->directory('about-page-history')
                                ->required(),
                        ])
                        ->defaultItems(1)
                        ->columns(2)
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                ]),
        ]);
    }

    public function submit(): void
    {
        // Re-initialize $this->aboutPage to ensure it's available on submission
        $this->aboutPage = Pages::firstOrCreate(['name' => 'About']);

        $data = $this->form->getState();

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
            ['name' => 'history', 'page_id' => $this->aboutPage->id],
            ['content' => $data['historyData']]
        );

        $this->notify('success', 'About page content updated successfully!');
    }
}
