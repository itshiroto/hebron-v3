<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DevotionResource\Pages;
use App\Filament\Resources\DevotionResource\RelationManagers;
use App\Models\Devotion;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DevotionResource extends Resource
{
    protected static ?string $model = Devotion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    TextInput::make('title')->required()->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                            if (($get('slug') ?? '') !== Str::slug($old)) {
                                return;
                            }

                            $set('slug', Str::slug($state));
                        }),
                    Select::make('category')
                        ->options([
                            'devotion' => "Renungan",
                            'news' => "Berita",
                            'announcement' => "Pengumuman",
                        ])
                        ->required(),
                    TextInput::make('slug')->required()->maxLength(255),

                    // DatePicker::make('published_at')->format('d/m/Y')->label("Published date")

                ]),
                Section::make()->columns(1)->schema([
                    RichEditor::make('content')
                ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListDevotions::route('/'),
            'create' => Pages\CreateDevotion::route('/create'),
            'edit' => Pages\EditDevotion::route('/{record}/edit'),
        ];
    }
}
