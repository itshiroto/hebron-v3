<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\DevotionResource\Pages\ListDevotions;
use App\Filament\Resources\DevotionResource\Pages\CreateDevotion;
use App\Filament\Resources\DevotionResource\Pages\EditDevotion;
use App\Filament\Resources\DevotionResource\Pages;
use App\Models\Devotion;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;

class DevotionResource extends Resource
{
    protected static ?string $model = Devotion::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->columns(2)->columnSpanFull()->schema([
                    TextInput::make('title')->required()->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                            if (($get('slug') ?? '') !== Str::slug($old)) {
                                return;
                            }

                            $set('slug', Str::slug($state));
                        }),
                    TextInput::make('slug')->required()->maxLength(255),
                    Select::make('category')
                        ->options([
                            'devotion' => "Renungan",
                            'news' => "Berita",
                            'announcement' => "Pengumuman",
                        ])
                        ->required(),
                    // DatePicker::make('published_at')->format('d/m/Y')->label("Published date")
                ]),

                Section::make()->columns(1)->columnSpanFull()->schema([
                    RichEditor::make('content')
                        ->fileAttachmentsDirectory('devotion-attachments'),
                    FileUpload::make('image')
                        ->label('Background Image')
                        ->image()
                        ->imageEditor()
                        ->directory('devotion-image')
                        ->disk('public')
                ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('category')->sortable()->badge(),
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
            'index' => ListDevotions::route('/'),
            'create' => CreateDevotion::route('/create'),
            'edit' => EditDevotion::route('/{record}/edit'),
        ];
    }
}
