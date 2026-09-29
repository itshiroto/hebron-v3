<?php

namespace App\Filament\Concerns;

use App\Models\Pages;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

/**
 * Adds an "SEO" section to a page editor, stored as the page's `meta` section.
 * Rendered server-side in resources/views/app.blade.php.
 */
trait HasSeoMeta
{
    protected function loadSeoMeta(Pages $page): array
    {
        return $page->sections()->firstOrCreate(
            ['name' => 'meta', 'page_id' => $page->id],
            ['content' => []]
        )->content;
    }

    protected function saveSeoMeta(Pages $page, array $meta): void
    {
        if (isset($meta['image']) && is_array($meta['image'])) {
            $meta['image'] = $meta['image'][0] ?? null;
        }

        $page->sections()->updateOrCreate(
            ['name' => 'meta', 'page_id' => $page->id],
            ['content' => $meta]
        );
    }

    protected function seoSection(): Section
    {
        return Section::make('SEO')
            ->description('Shown in search results and link previews (WhatsApp, Facebook, etc.). Leave empty to use the defaults.')
            ->collapsible()
            ->schema([
                TextInput::make('metaData.title')
                    ->label('Page Title')
                    ->maxLength(60)
                    ->helperText('Browser tab and search result title. Around 50–60 characters.'),
                Textarea::make('metaData.description')
                    ->label('Meta Description')
                    ->rows(3)
                    ->maxLength(160)
                    ->helperText('Summary shown under the title in search results. Around 150–160 characters.'),
                FileUpload::make('metaData.image')
                    ->label('Share Image')
                    ->image()
                    ->imageEditor()
                    ->directory('seo')
                    ->helperText('Image for link previews. 1200×630 recommended. Falls back to the church logo.'),
            ]);
    }
}
