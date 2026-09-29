<?php

namespace App\Filament\Resources\DivisionResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\DivisionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDivisions extends ListRecords
{
    protected static string $resource = DivisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
