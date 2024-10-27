<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MembersResource\Pages;
use App\Filament\Resources\MembersResource\RelationManagers;
use App\Models\Members;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MembersResource extends Resource
{
    protected static ?string $model = Members::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Resource';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),

                Forms\Components\Select::make('team_id')
                    ->label('Team')
                    ->searchable()
                    ->multiple()
                    ->columnSpanFull()
                    ->required()
                    ->preload()
                    ->relationship('teams', 'name'),

                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),

                Forms\Components\FileUpload::make('image')
                    ->label('Photo')
                    ->image()
                    ->required()
                    ->columnSpanFull(),

                    Forms\Components\Repeater::make('social_media') // Menggunakan repeater untuk input teks yang dinamis
                        ->label('Social Media')
                        ->schema([
                            Forms\Components\TextInput::make('url')
                            ->label('Website')
                            ->url() // Validasi URL
                            ->prefixIcon('heroicon-o-globe-alt') // Menggunakan ikon globe bawaan dari Heroicons
                            ->placeholder('https://example.com') // Contoh URL placeholder
                        ])
                        ->minItems(1)
                        ->maxItems(5)
                        ->columnSpanFull()
                        ->collapsible() // Menampilkan input teks yang dapat dilipat
                        ->createItemButtonLabel('Add More'), // Label untuk tombol tambah
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->sortable(),

                Tables\Columns\TextColumn::make('email')
                ->label('Email')
                ->searchable()
                ->sortable(),

                Tables\Columns\TextColumn::make('teams.name')
                ->label('Team')
                ->badge()
                ->searchable()
                ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ])
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
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMembers::route('/create'),
            'edit' => Pages\EditMembers::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
