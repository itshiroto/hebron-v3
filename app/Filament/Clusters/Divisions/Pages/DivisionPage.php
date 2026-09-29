<?php

namespace App\Filament\Clusters\Divisions\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use App\Filament\Clusters\Divisions;
use App\Models\Pages;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class DivisionPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';
    protected string $view = 'filament.clusters.divisions.pages.division-page';
    protected static ?string $cluster = Divisions::class;
    protected static ?string $title = 'Division Page Content';

    public ?array $heroData = [];

    protected Pages $divisionPageModel;

    public function mount(): void
    {
        $this->divisionPageModel = Pages::firstOrCreate(['name' => 'Division Page']);

        $heroSection = $this->divisionPageModel->sections()->firstOrCreate(
            ['name' => 'hero', 'page_id' => $this->divisionPageModel->id],
            ['content' => []]
        );
        $this->heroData = $heroSection->content;

        $this->form->fill([
            'heroData' => $this->heroData,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
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
                        ->directory('division-page-hero')
                        ->required(),
                ])
        ]);
    }

    public function submit(): void
    {
        $this->divisionPageModel = Pages::firstOrCreate(['name' => 'Division Page']);

        $data = $this->form->getState();

        if (isset($data['heroData']['image']) && is_array($data['heroData']['image'])) {
            $data['heroData']['image'] = $data['heroData']['image'][0] ?? null;
        }

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
