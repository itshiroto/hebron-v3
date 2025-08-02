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
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class MinistryPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.ministry-page';

    protected static ?string $title = 'Ministries Page';
    protected static ?string $navigationGroup = 'Pages';

    public ?array $heroData = [];
    public ?array $introData = [];
    public ?array $serveData = [];
    public ?array $faqData = [];

    protected ?Pages $ministryPage = null;

    public function mount(): void
    {
        $this->ministryPage = Pages::firstOrCreate(['name' => 'Ministries Page']);

        $heroSection = $this->ministryPage->sections()->firstOrCreate(
            ['name' => 'hero', 'page_id' => $this->ministryPage->id],
            ['content' => []]
        );
        $this->heroData = $heroSection->content;

        $introSection = $this->ministryPage->sections()->firstOrCreate(
            ['name' => 'intro', 'page_id' => $this->ministryPage->id],
            ['content' => []]
        );
        $this->introData = $introSection->content;

        $serveSection = $this->ministryPage->sections()->firstOrCreate(
            ['name' => 'serve', 'page_id' => $this->ministryPage->id],
            ['content' => []]
        );
        $this->serveData = $serveSection->content;

        $faqSection = $this->ministryPage->sections()->firstOrCreate(
            ['name' => 'faq', 'page_id' => $this->ministryPage->id],
            ['content' => []]
        );
        $this->faqData = $faqSection->content;

        $this->form->fill([
            'heroData' => $this->heroData,
            'introData' => $this->introData,
            'serveData' => $this->serveData,
            'faqData' => $this->faqData,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Hero Section')
                ->description('Background image and title for the hero section.')
                ->schema([
                    TextInput::make('heroData.title')
                        ->label('Title')
                        ->required(),
                    FileUpload::make('heroData.image')
                        ->label('Background Image')
                        ->image()
                        ->imageEditor()
                        ->directory('ministry-page-hero')
                        ->required(),
                ]),
            Section::make('Intro Section')
                ->description('Title and description for the introduction section.')
                ->schema([
                    TextInput::make('introData.title')
                        ->label('Title')
                        ->required(),
                    RichEditor::make('introData.desc')
                        ->label('Description')
                        ->required(),
                ]),
            Section::make('Serve Section')
                ->description('List of ministries where people can serve.')
                ->schema([
                    TextInput::make('serveData.title')
                        ->label('Title')
                        ->required(),
                    Repeater::make('serveData.ministryList')
                        ->label('Ministry Items')
                        ->schema([
                            TextInput::make('title')
                                ->label('Ministry Title')
                                ->required(),
                            RichEditor::make('desc')
                                ->label('Description')
                                ->required(),
                            FileUpload::make('image')
                                ->label('Image')
                                ->image()
                                ->imageEditor()
                                ->directory('ministry-page-ministries')
                                ->required(),
                            TextInput::make('link')
                                ->label('Volunteer Link')
                                ->url()
                                ->required(),
                        ])
                        ->defaultItems(1)
                        ->columns(2)
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                ]),
            Section::make('FAQ Section')
                ->description('Frequently Asked Questions for the Ministries page.')
                ->schema([
                    TextInput::make('faqData.title')
                        ->label('Title')
                        ->required(),
                    Repeater::make('faqData.questionsList')
                        ->label('Questions and Answers')
                        ->schema([
                            TextInput::make('question')
                                ->label('Question')
                                ->required(),
                            RichEditor::make('answer')
                                ->label('Answer')
                                ->required(),
                        ])
                        ->defaultItems(1)
                        ->columns(1)
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['question'] ?? null),
                ]),
        ]);
    }

    public function submit(): void
    {
        try {
            $this->ministryPage = Pages::firstOrCreate(['name' => 'Ministries Page']);
            $data = $this->form->getState();

            // Handle image paths for heroData
            if (isset($data['heroData']['image']) && is_array($data['heroData']['image'])) {
                $data['heroData']['image'] = $data['heroData']['image'][0] ?? null;
            }

            // Handle image paths for serveData (within repeater)
            if (isset($data['serveData']['ministryList']) && is_array($data['serveData']['ministryList'])) {
                foreach ($data['serveData']['ministryList'] as $key => $item) {
                    if (isset($item['image']) && is_array($item['image'])) {
                        $data['serveData']['ministryList'][$key]['image'] = $item['image'][0] ?? null;
                    }
                }
            }

            DB::transaction(function () use ($data) {
                $sectionsToUpdate = [
                    'hero' => $data['heroData'],
                    'intro' => $data['introData'],
                    'serve' => $data['serveData'],
                    'faq' => $data['faqData'],
                ];

                foreach ($sectionsToUpdate as $name => $content) {
                    Sections::updateOrCreate(
                        ['page_id' => $this->ministryPage->id, 'name' => $name],
                        ['content' => $content] // Content is already an array, will be cast to JSON
                    );
                }
            });

            Notification::make()
                ->title('Ministries Page updated successfully!')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error updating Ministries Page.')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
