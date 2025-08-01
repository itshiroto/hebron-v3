<?php

namespace App\Filament\Pages;

use App\Models\Pages;
use Filament\Pages\Page;

class ContactPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.contact-page';

    protected $page;
    public ?array $contactData = [];

    public function mount(): void
    {
        $this->page = Pages::firstOrCreate(['name' => 'About']);

        $heroSection = $this->page->sections()->firstOrCreate(
            ['name' => 'contactSection', 'page_id' => $this->aboutPage->id],
            ['content' => []]
        );

        // Ensure the form is filled with data from the properties
        $this->form->fill([
            'heroData' => $this->heroData,
            'visionMissionData' => $this->visionMissionData,
            'valuesData' => $this->valuesData,
            'historyData' => $this->historyData,
        ]);
    }
}
