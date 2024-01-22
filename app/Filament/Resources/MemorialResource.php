<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\Memorial as MemorialHelper;
use App\Filament\Helpers\Trashed;
use App\Filament\RelationManagers\AuditsRelationManager;
use App\Filament\Resources\MemorialResource\Pages;
use App\Models\Memorial;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class MemorialResource extends Resource
{
    protected static ?string $model = Memorial::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    protected static int $globalSearchResultsLimit = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ...MemorialHelper::form(true),
            ]);
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
                    ->label(__('Image'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('location.name')
                    ->label(__('Location'))
                    ->url(
                        url: fn ($record) => $record->location_id ? LocationResource::getUrl('edit',
                            [$record->location_id]) : null,
                        shouldOpenInNewTab: true
                    )
                    ->color('primary')
                    ->alignment(Alignment::Center)
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('people_count')
                    ->counts('people')
                    ->url(fn ($record): string => PersonResource::getUrl('index', ['tableFilters[memorial_id][value]' => $record->id]))
                    ->color('primary')
                    ->label(__('People'))
                    ->alignment(Alignment::Center)
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('slug')
                    ->sortable()
                    ->searchable()
                    ->alignment(Alignment::Center)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date')
                    ->label(__('Date'))
                    ->dateTime()
                    ->sortable()
                    ->alignment(Alignment::Center)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label(__('Deleted at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->emptyStateHeading(__('No memorials'))
            ->paginated([10, 25, 50, 100, 500])
            ->filters([
                Tables\Filters\SelectFilter::make('location_id')
                    ->searchable()
                    ->relationship('location', 'name')
                    ->label(__('Location')),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->deferFilters()
            ->filtersApplyAction(
                fn (Action $action) => $action
                    ->link()
                    ->button()
                    ->label(__('Apply filters')),
            )
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    ...Trashed::actions(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ...Trashed::bulkActions(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMemorials::route('/'),
            'create' => Pages\CreateMemorial::route('/create'),
            'edit' => Pages\EditMemorial::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('Memorials');
    }

    public static function getRelations(): array
    {
        return [
            AuditsRelationManager::class,
        ];
    }

    public static function getPluralModelLabel(): string
    {
        return __('Memorials');
    }
}
