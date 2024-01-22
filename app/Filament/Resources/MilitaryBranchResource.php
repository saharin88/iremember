<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MilitaryBranchResource\Pages;
use App\Models\MilitaryBranch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;

class MilitaryBranchResource extends Resource
{
    protected static ?string $model = MilitaryBranch::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->translateLabel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('parent_id')
                    ->label(__('Parent'))
                    ->relationship('parent', 'name'),
                Forms\Components\FileUpload::make('image')
                    ->directory('military-branches')
                    ->translateLabel()
                    ->image()
                    ->imageEditor()
                    ->openable(),
                Forms\Components\RichEditor::make('description')
                    ->translateLabel(),
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
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\ImageColumn::make('image')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('parent.name')
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
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                ]),
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
            'index' => Pages\ManageMilitaryBranches::route('/'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('Military branches');
    }

    public static function getNavigationParentItem(): ?string
    {
        return __('Units');
    }
}
