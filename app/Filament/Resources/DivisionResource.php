<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Divisions;
use App\Filament\Resources\DivisionResource\Pages;
use App\Models\Division;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class DivisionResource extends Resource
{
    protected static ?string $model = Division::class;

    protected static ?string $cluster = Divisions::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Division List';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->columns(1)->schema([
                    TextInput::make('title')->required()->maxLength(255),
                    Textarea::make('description')->required()->maxLength(255)->rows(5),
                    TextInput::make('time')->required()->maxLength(255),
                    TextInput::make('link')->maxLength(255),
                    FileUpload::make('image')
                        ->label('Image')
                        ->image()
                        ->imageEditor()
                        ->directory('divisions-image')
                        ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                ImageColumn::make('image'),
                TextColumn::make('time')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListDivisions::route('/'),
            'create' => Pages\CreateDivision::route('/create'),
            'edit' => Pages\EditDivision::route('/{record}/edit'),
        ];
    }
}
