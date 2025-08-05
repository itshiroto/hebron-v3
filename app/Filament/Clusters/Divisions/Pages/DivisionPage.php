<?php

namespace App\Filament\Clusters\Divisions\Pages;

use App\Filament\Clusters\Divisions;
use App\Models\Pages; // Import the Pages model
use Filament\Forms\Components\FileUpload; // Import FileUpload
use Filament\Forms\Components\Section; // Import Section
use Filament\Forms\Components\TextInput; // Import TextInput
use Filament\Forms\Concerns\InteractsWithForms; // Import InteractsWithForms
use Filament\Forms\Contracts\HasForms; // Import HasForms
use Filament\Forms\Form; // Import Form
use Filament\Notifications\Notification; // Import Notification
use Filament\Pages\Page;

class DivisionPage extends Page implements HasForms // Implement HasForms
{
    use InteractsWithForms; // Use InteractsWithForms trait

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.clusters.divisions.pages.division-page';

    protected static ?string $cluster = Divisions::class;

    protected static ?string $title = 'Division Page Content'; // Custom title for the page
    protected static ?string $navigationGroup = 'Divisions'; // Optional: Group under Divisions

    public ?array $heroData = []; // Property to hold hero section data

    protected Pages $divisionPageModel; // Property to hold the Pages model instance

    public function mount(): void
    {
        // Find or create the 'Pages' entry for this specific division page content
        // Assuming a generic 'Division Page' for now. Adjust if dynamic per division.
        $this->divisionPageModel = Pages::firstOrCreate(['name' => 'Division Page']);

        // Find or create the 'hero' section for this page
        $heroSection = $this->divisionPageModel->sections()->firstOrCreate(
            ['name' => 'hero', 'page_id' => $this->divisionPageModel->id],
            ['content' => []] // Default empty content if new
        );
        $this->heroData = $heroSection->content; // Load existing content

        // Fill the form with the retrieved data
        $this->form->fill([
            'heroData' => $this->heroData,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Hero Section')
                ->description('Configure the main hero section for the Division Page.')
                ->schema([
                    TextInput::make('heroData.title')
                        ->label('Title')
                        ->required(),
                    FileUpload::make('heroData.image')
                        ->label('Background Image')
                        ->image()
                        ->imageEditor()
                        ->directory('division-page-hero') // Directory for uploaded images
                        ->required(),
                ])
        ]);
    }

    public function submit(): void
    {
        // Re-fetch or ensure the page model is loaded
        $this->divisionPageModel = Pages::firstOrCreate(['name' => 'Division Page']);

        $data = $this->form->getState();

        // Handle image path for heroData (FileUpload returns an array)
        if (isset($data['heroData']['image']) && is_array($data['heroData']['image'])) {
            $data['heroData']['image'] = $data['heroData']['image'][0] ?? null;
        }

        // Update or create the 'hero' section content in the database
        $this->divisionPageModel->sections()->updateOrCreate(
            ['name' => 'hero', 'page_id' => $this->divisionPageModel->id],
            ['content' => $data['heroData']]
        );

        Notification::make()
            ->title('Division Page Hero Section updated successfully!')
            ->success()
            ->send();
    }
}
