<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\Images;
use App\Filament\Helpers\Location;
use App\Filament\RelationManagers\AuditsRelationManager;
use App\Filament\Resources\BattleResource\Pages;
use App\Filament\Resources\BattleResource\RelationManagers;
use App\Models\Battle;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class BattleResource extends Resource
{
    protected static ?string $model = Battle::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'name';

    protected static int $globalSearchResultsLimit = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([

                        Forms\Components\Group::make([
                            Forms\Components\TextInput::make('name')
                                ->label('Name')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('slug')
                                ->unique(ignoreRecord: true)
                                ->rule('alpha_dash:ascii')
                                ->placeholder(__('Will be generated automatically from name')),
                            Forms\Components\Select::make('location_id')
                                ->label('Location')
                                ->relationship('location', 'name')
                                ->searchable()
                                ->createOptionForm(Location::form())
                                ->editOptionForm(Location::form()),
                            Forms\Components\Group::make([
                                Forms\Components\DatePicker::make('start')
                                    ->label(__('Start date')),
                                Forms\Components\DatePicker::make('end')
                                    ->label(__('End date')),
                            ])->columns(2),
                        ])
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 2,
                                'lg' => 2,
                                'xl' => 2,
                            ]),

                        Forms\Components\FileUpload::make('image')
                            ->label(__('Main image'))
                            ->directory('memorials')
                            ->getUploadedFileNameForStorageUsing(function (
                                TemporaryUploadedFile $file,
                                Forms\Get $get,
                            ) {
                                $slug = $get('slug') ?: SlugService::createSlug(\App\Models\Memorial::class, 'slug',
                                    $get('name'));

                                return $slug.'.'.$file->guessExtension();
                            })
                            ->imagePreviewHeight('310')
                            ->openable()
                            ->image()
                            ->imageEditor()
                            ->columnSpan(1),

                        Forms\Components\RichEditor::make('description')
                            ->label(__('Description'))
                            ->columnSpanFull(),

                    ])
                    ->columnSpanFull()
                    ->columns([
                        'sm' => 1,
                        'md' => 3,
                        'lg' => 3,
                        'xl' => 3,
                    ]),

                ...Images::form(Battle::class),

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
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('people_count')
                    ->counts('people')
                    ->url(fn ($record): string => PersonResource::getUrl('index', ['tableFilters[battle_id][value]' => $record->id]))
                    ->color('primary')
                    ->label(__('People'))
                    ->alignment(Alignment::Center)
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('slug')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('start')
                    ->label(__('Start date'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('end')
                    ->label(__('End date'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->emptyStateHeading(__('No battles'))
            ->paginated([10, 25, 50, 100, 500])
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
            RelationManagers\PeopleRelationManager::class,
            AuditsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBattles::route('/'),
            'create' => Pages\CreateBattle::route('/create'),
            'edit' => Pages\EditBattle::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('Battles');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Battles');
    }
}
