<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\PastorResource\Pages\ListPastors;
use App\Filament\Resources\PastorResource\Pages\CreatePastor;
use App\Filament\Resources\PastorResource\Pages\EditPastor;
use App\Filament\Resources\PastorResource\Pages;
use App\Models\Pastor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PastorResource extends Resource
{
    protected static ?string $model = Pastor::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-circle';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('position')->required()->maxLength(255),
                FileUpload::make('image')
                    ->label('Image')
                    ->image()
                    ->imageEditor()
                    ->directory('pastors-image')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('position')->sortable(),
                ImageColumn::make('image')->circular(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPastors::route('/'),
            'create' => CreatePastor::route('/create'),
            'edit' => EditPastor::route('/{record}/edit'),
        ];
    }
}
