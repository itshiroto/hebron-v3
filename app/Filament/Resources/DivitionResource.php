<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DivitionResource\Pages;
use App\Models\Divition;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class DivitionResource extends Resource
{
    protected static ?string $model = Divition::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')->required()->maxLength(255),
                TextInput::make('description')->required()->maxLength(255),
                FileUpload::make('image')
                    ->label('Image')
                    ->image()
                    ->imageEditor()
                    ->directory('divitions-image')
                    ->required(),
                TextInput::make('time')->required()->maxLength(255),
                TextInput::make('link')->required()->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('description')->sortable(),
                ImageColumn::make('image')->circular(),
                TextColumn::make('time')->sortable(),
                TextColumn::make('link')->sortable(),
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
            'index' => Pages\ListDivitions::route('/'),
            'create' => Pages\CreateDivition::route('/create'),
            'edit' => Pages\EditDivition::route('/{record}/edit'),
        ];
    }
}
