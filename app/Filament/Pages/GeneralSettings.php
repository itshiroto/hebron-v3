<?php

namespace App\Filament\Pages;

use App\Models\Pages;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class GeneralSettings extends Page implements HasForms
{
    use InteractsWithForms;

    // protected static ?string $navigationIcon = 'heroicon-o-cog'; // Changed icon for settings

    protected static string $view = 'filament.pages.general-settings';

    protected static ?string $title = 'General';
    protected static ?string $navigationGroup = 'Settings'; // Added navigation group
    protected static ?int $navigationSort = 99;

    public ?array $generalSettingsData = [];

    protected $settingsPage;

    public function mount(): void
    {
        // Find or create a 'Settings' page entry in the 'pages' table
        $this->settingsPage = Pages::firstOrCreate(['name' => 'Settings']);

        // Find or create the 'general_settings' section for this page
        $generalSettingsSection = $this->settingsPage->sections()->firstOrCreate(
            ['name' => 'general_settings', 'page_id' => $this->settingsPage->id],
            ['content' => []]
        );

        // Load the content into the form data
        $this->generalSettingsData = $generalSettingsSection->content;

        // Fill the form with the retrieved data
        $this->form->fill([
            'generalSettingsData' => $this->generalSettingsData,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Site Information')
                ->description('Configure general information about your church.')
                ->schema([
                    TextInput::make('generalSettingsData.church_name')
                        ->label('Church Name')
                        ->required(),
                    TextInput::make('generalSettingsData.church_subtitle')
                        ->label('Church Subtitle')
                        ->required(),
                    FileUpload::make('generalSettingsData.church_logo')
                        ->label('Church Logo')
                        ->image()
                        ->imageEditor()
                        ->directory('site-settings')
                        ->required(),
                    RichEditor::make('generalSettingsData.church_address')
                        ->label('Church Address')
                        ->required(),
                    TextInput::make('generalSettingsData.maps_url')
                        ->label('Google Maps link (Petunjuk arah)')
                        ->url(),
                    TextInput::make('generalSettingsData.livestream_url')
                        ->label('Live streaming link (Tonton Online)')
                        ->url(),
                ]),
            Section::make('Social Media Accounts')
                ->description('Add links to your social media profiles.')
                ->schema([
                    Repeater::make('generalSettingsData.social_media')
                        ->label('Social Media Links')
                        ->schema([
                            TextInput::make('platform')
                                ->label('Platform (e.g., Facebook, Instagram, YouTube)')
                                ->required(),
                            TextInput::make('url')
                                ->label('URL')
                                ->url() // Validate as URL
                                ->required(),
                        ])
                        ->defaultItems(1)
                        ->columns(2)
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['platform'] ?? null),
                ]),
        ]);
    }

    public function submit(): void
    {
        // Ensure the settings page exists
        $this->settingsPage = Pages::firstOrCreate(['name' => 'Settings']);

        // Get the validated and processed data from the form
        $data = $this->form->getState();

        // Handle image path for church_logo
        if (isset($data['generalSettingsData']['church_logo']) && is_array($data['generalSettingsData']['church_logo'])) {
            $data['generalSettingsData']['church_logo'] = $data['generalSettingsData']['church_logo'][0] ?? null;
        }

        // Update or create the 'general_settings' section content
        $this->settingsPage->sections()->updateOrCreate(
            ['name' => 'general_settings', 'page_id' => $this->settingsPage->id],
            ['content' => $data['generalSettingsData']]
        );

        Notification::make()
            ->title('General settings updated successfully!')
            ->success()
            ->send();
    }
}
