<?php

namespace App\Filament\Clusters\Divisions\Pages;

use App\Filament\Clusters\Divisions;
use Filament\Pages\Page;

class DivisionPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.clusters.divisions.pages.division-page';

    protected static ?string $cluster = Divisions::class;
}
