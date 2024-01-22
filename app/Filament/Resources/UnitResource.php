<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\Trashed;
use App\Filament\Helpers\Unit as UnitHelper;
use App\Filament\RelationManagers\AuditsRelationManager;
use App\Filament\Resources\UnitResource\Pages;
use App\Models\Unit;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    protected static int $globalSearchResultsLimit = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ...UnitHelper::form(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label(__('Image'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('people_count')
                    ->counts('people')
                    ->url(fn ($record): string => PersonResource::getUrl('index', ['tableFilters[unit_id][value]' => $record->id]))
                    ->color('primary')
                    ->label(__('People'))
                    ->alignment(Alignment::Center)
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('militaryBranch.name')
                    ->translateLabel()
                    ->color(fn (Unit $record) => $record->militaryBranch ? false : 'gray')
                    ->default(__('Not indicated')),
                Tables\Columns\TextColumn::make('full_name')
                    ->label(__('Full title'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('slug')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label(__('Parent'))
                    ->url(
                        url: fn ($record) => $record->parent_id ? self::getUrl('edit', [$record->parent_id]) : null,
                        shouldOpenInNewTab: true
                    )
                    ->color('info')
                    ->sortable()
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
            ->emptyStateHeading(__('No units'))
            ->paginated([10, 25, 50, 100, 500])
            ->filters([
                Tables\Filters\SelectFilter::make('military_branch_id')
                    ->relationship('militaryBranch', 'name')
                    ->label(__('Military branch')),
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
            'index' => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'edit' => Pages\EditUnit::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('Units');
    }

    public static function getRelations(): array
    {
        return [
            AuditsRelationManager::class,
        ];
    }

    public static function getPluralModelLabel(): string
    {
        return __('Units');
    }
}
