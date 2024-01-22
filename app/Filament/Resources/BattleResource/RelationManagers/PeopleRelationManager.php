<?php

namespace App\Filament\Resources\BattleResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PeopleRelationManager extends RelationManager
{
    protected static string $relationship = 'people';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('person_id')
                    ->relationship('people', 'full_name')
                    ->searchable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('full_name')
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->translateLabel()
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('full_name')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('birth')
                    ->label(__('Date of birth'))
                    ->date('d.m.Y')
                    ->sortable()
                    ->toggleable()
                    ->alignment(Alignment::Center),
                Tables\Columns\TextColumn::make('death')
                    ->label(__('Date of death'))
                    ->date('d.m.Y')
                    ->sortable()
                    ->toggleable()
                    ->alignment(Alignment::Center),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('People');
    }
}
