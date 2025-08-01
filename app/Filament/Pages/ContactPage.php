<?php

namespace App\Filament\Pages;

use App\Models\Pages;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ContactPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.contact-page';

    protected static ?string $title = 'Contact Us';
    protected static ?string $navigationGroup = 'Pages';

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
            'heroData' => $this->heroData,
            'contactListData' => $this->contactListData,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Hero Section')
                ->description('Configure the main hero section of the Contact page.')
                ->schema([
                    TextInput::make('heroData.title')
                        ->label('Title')
                        ->required(),
                    TextInput::make('heroData.subtitle')
                        ->label('Subtitle')
                        ->required(),
                ]),

            Section::make('Contact List')
                ->description('List of contact information.')
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
        ]);
    }

    public function submit(): void
    {
        $this->contactPage = Pages::firstOrCreate(['name' => 'Contact']);

        $data = $this->form->getState();

        $this->contactPage->sections()->updateOrCreate(
            ['name' => 'hero', 'page_id' => $this->contactPage->id],
            ['content' => $data['heroData']]
        );

        $this->contactPage->sections()->updateOrCreate(
            ['name' => 'contact_list', 'page_id' => $this->contactPage->id],
            ['content' => $data['contactListData']]
        );

        Notification::make()
            ->title('Contact page content updated successfully!')
            ->success()
            ->send();
    }
}
