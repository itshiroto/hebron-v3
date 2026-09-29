<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\HasSeoMeta;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use App\Models\Pages;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ContactPage extends Page implements HasForms
{
    use InteractsWithForms, HasSeoMeta;

    public ?array $metaData = [];

    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.pages.contact-page';

    protected static ?string $title = 'Contact Us';
    protected static string | \UnitEnum | null $navigationGroup = 'Pages';
    protected static ?int $navigationSort = 4;

    public ?array $heroData = [];
    public ?array $contactListData = [];

    protected $contactPage;

    public function mount(): void
    {
        $this->contactPage = Pages::firstOrCreate(['name' => 'Contact']);

        $heroSection = $this->contactPage->sections()->firstOrCreate(
            ['name' => 'hero', 'page_id' => $this->contactPage->id],
            ['content' => []]
        );
        $this->heroData = $heroSection->content;

        $contactListSection = $this->contactPage->sections()->firstOrCreate(
            ['name' => 'contact_list', 'page_id' => $this->contactPage->id],
            ['content' => []]
        );
        $this->contactListData = $contactListSection->content;

        $this->form->fill([
            'metaData' => $this->loadSeoMeta($this->contactPage),
            'heroData' => $this->heroData,
            'contactListData' => $this->contactListData,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Hero Section')
                ->description('Configure the main hero section of the Contact page.')
                ->schema([
                    TextInput::make('heroData.title')
                        ->label('Title')
                        ->placeholder('Kami senang bertemu denganmu'),
                    TextInput::make('heroData.subtitle')
                        ->label('Subtitle'),
                    FileUpload::make('heroData.image')
                        ->label('Background Image')
                        ->image()
                        ->imageEditor()
                        ->directory('contact-page-hero')
                        ->required()
                ]),

            Section::make('Contact List')
                ->description('Shown under "Hubungi Kami". Address, map link and service times come from General Settings and the Home schedule.')
                ->schema([
                    Repeater::make('contactListData')
                        ->label('Contact Items')
                        ->schema([
                            TextInput::make('title')
                                ->label('Name')
                                ->required(),
                            TextInput::make('desc')
                                ->label('Value')
                                ->required(),
                            TextInput::make('link')
                                ->label('Link (Optional)')
                                ->url(),
                        ])
                        ->defaultItems(1)
                        ->columns(1)
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                ]),
            $this->seoSection(),
        ]);
    }

    public function submit(): void
    {
        $this->contactPage = Pages::firstOrCreate(['name' => 'Contact']);

        $data = $this->form->getState();

        // Handle image paths for heroData
        if (isset($data['heroData']['image']) && is_array($data['heroData']['image'])) {
            $data['heroData']['image'] = $data['heroData']['image'][0] ?? null;
        }

        $this->contactPage->sections()->updateOrCreate(
            ['name' => 'hero', 'page_id' => $this->contactPage->id],
            ['content' => $data['heroData']]
        );

        $this->contactPage->sections()->updateOrCreate(
            ['name' => 'contact_list', 'page_id' => $this->contactPage->id],
            ['content' => $data['contactListData']]
        );

        $this->saveSeoMeta($this->contactPage, $data['metaData'] ?? []);

        Notification::make()
            ->title('Contact page content updated successfully!')
            ->success()
            ->send();
    }
}
