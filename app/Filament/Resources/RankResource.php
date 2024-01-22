<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RankResource\Pages;
use App\Models\Rank;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;

class RankResource extends Resource
{
    protected static ?string $model = Rank::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->translateLabel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('military_branch_id')
                    ->label(__('Military branch'))
                    ->searchable()
                    ->relationship('militaryBranch', 'name'),
                Forms\Components\FileUpload::make('image')
                    ->translateLabel()
                    ->directory('ranks')
                    ->image()
                    ->imageEditor()
                    ->openable(),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->Label(__('ID'))
                    ->sortable()
                    ->alignment(Alignment::Center)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ImageColumn::make('image')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('people_count')
                    ->counts('people')
                    ->url(fn ($record): string => PersonResource::getUrl('index', ['tableFilters[rank_id][value]' => $record->id]))
                    ->color('primary')
                    ->label(__('People'))
                    ->alignment(Alignment::Center)
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('militaryBranch.name')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->paginated([10, 25, 50, 100, 500])
            ->emptyStateHeading(__('No ranks'))
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRanks::route('/'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('Ranks');
    }

    public static function getNavigationParentItem(): ?string
    {
        return __('Units');
    }
}
