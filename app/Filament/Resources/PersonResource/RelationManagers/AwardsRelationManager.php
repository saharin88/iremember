<?php

namespace App\Filament\Resources\PersonResource\RelationManagers;

use App\Filament\Helpers\Award as AwardHelper;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AwardsRelationManager extends RelationManager
{
    protected static string $relationship = 'awards';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                ...AwardHelper::form(),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\DatePicker::make('date')
                            ->after('birth')
                            ->columnSpan(1)
                            ->label(__('Date of Award')),
                        Forms\Components\RichEditor::make('additional_info')
                            ->columnSpanFull()
                            ->label(__('Information about the Award')),
                    ])->columnSpanFull()->columns(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Award')),
                Tables\Columns\TextColumn::make('date')
                    ->label(__('Date of Award')),
            ])
            ->emptyStateHeading(__('No awards'))
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
        return __('Awards');
    }
}
