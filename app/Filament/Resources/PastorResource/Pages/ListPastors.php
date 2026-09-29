<?php

namespace App\Filament\Resources\PastorResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\PastorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPastors extends ListRecords
{
    protected static string $resource = PastorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
