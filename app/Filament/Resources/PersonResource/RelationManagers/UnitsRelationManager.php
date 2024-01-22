<?php

namespace App\Filament\Resources\PersonResource\RelationManagers;

use App\Filament\Helpers\Unit as UnitHelper;
use App\Models\MilitaryPosition;
use App\Models\Rank;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UnitsRelationManager extends RelationManager
{
    protected static string $relationship = 'units';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                ...UnitHelper::form(),
                Forms\Components\DatePicker::make('start')
                    ->before('end')
                    ->label(__('Start Date')),
                Forms\Components\DatePicker::make('end')
                    ->after('start')
                    ->label(__('End Date')),
                Forms\Components\Select::make('rank_id')
                    ->options(fn () => Rank::all()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->label(__('Rank')),
                Forms\Components\Select::make('military_position_id')
                    ->options(fn () => MilitaryPosition::all()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->label(__('Position')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start')
                    ->label(__('Start Date'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('end')
                    ->label(__('End Date'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('rank_id')
                    ->formatStateUsing(fn ($state) => Rank::select('name')->find($state)->name)
                    ->label(__('Rank'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('military_position_id')
                    ->formatStateUsing(fn ($state) => MilitaryPosition::select('name')->find($state)->name)
                    ->label(__('Position'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->emptyStateHeading(__('No units'))
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\DetachAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Units');
    }
}
