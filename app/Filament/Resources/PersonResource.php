<?php

namespace App\Filament\Resources;

use App\Enums\Citizenship;
use App\Enums\Sex;
use App\Enums\State;
use App\Filament\Helpers\Filter;
use App\Filament\Helpers\Images;
use App\Filament\Helpers\Location;
use App\Filament\Helpers\Person as PersonHelper;
use App\Filament\Helpers\Rank;
use App\Filament\Helpers\Trashed;
use App\Filament\Helpers\Unit;
use App\Filament\RelationManagers\AuditsRelationManager;
use App\Filament\Resources\PersonResource\Pages;
use App\Filament\Resources\PersonResource\RelationManagers;
use App\Models\Person;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PersonResource extends Resource
{
    protected static ?string $breadcrumb = null;

    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static int $globalSearchResultsLimit = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->label(__('First name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->label(__('Last name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('middle_name')
                            ->label(__('Middle name'))
                            ->maxLength(255),
                    ])
                    ->columnSpanFull()
                    ->columns(3),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([

                                Forms\Components\FileUpload::make('photo')
                                    ->label(__('Main photo'))
                                    ->directory('persons')
                                    ->getUploadedFileNameForStorageUsing(function (
                                        TemporaryUploadedFile $file,
                                        Forms\Get $get,
                                    ) {
                                        $slug = $get('slug') ?: SlugService::createSlug(Person::class,
                                            'slug',
                                            implode(' ', [
                                                $get('first_name'),
                                                $get('last_name'),
                                                $get('middle_name'),
                                            ])
                                        );

                                        return $slug.'.'.$file->guessExtension();
                                    })
                                    ->imagePreviewHeight('330')
                                    ->openable()
                                    ->image()
                                    ->imageEditor(),

                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\ToggleButtons::make('sex')
                                            ->label(__('Sex'))
                                            ->options(Sex::class)
                                            ->default(Sex::MALE)
                                            ->inline(),
                                        Forms\Components\ToggleButtons::make('civil')
                                            ->label(__('Civil'))
                                            ->options([
                                                1 => __('Yes'),
                                                0 => __('No'),
                                            ])
                                            ->default(0)
                                            ->inline(),
                                    ])->columns(2),

                                Forms\Components\Select::make('citizenship')
                                    ->label(__('Citizenship'))
                                    ->options(Citizenship::class)
                                    ->default(Citizenship::UA),

                            ])->columnSpan(1),

                        Forms\Components\Group::make()
                            ->schema([

                                Forms\Components\Group::make([
                                    Forms\Components\TextInput::make('call_sign')
                                        ->label(__('Call sign'))
                                        ->maxLength(255)
                                        ->columnSpan(1),
                                    Forms\Components\TextInput::make('slug')
                                        ->unique(ignoreRecord: true)
                                        ->rule('alpha_dash:ascii')
                                        ->placeholder(__('Will be generated automatically from full name'))
                                        ->columnSpan(2),
                                ])->columns(3),

                                Forms\Components\RichEditor::make('death_details')
                                    ->toolbarButtons([
                                        'bold', 'italic', 'underline', 'strike', 'link', 'clean', 'undo', 'redo',
                                    ])
                                    ->label(__('Death details'))
                                    ->columnSpanFull(),
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Select::make('rank_id')
                                            ->relationship(name: 'rank', titleAttribute: 'name')
                                            ->label(__('Rank'))
                                            ->searchable()
                                            ->createOptionForm(Rank::form())
                                            ->editOptionForm(Rank::form())
                                            ->columnSpan(1),
                                        Forms\Components\Select::make('military_position_id')
                                            ->relationship(name: 'militaryPosition', titleAttribute: 'name')
                                            ->label(__('Military position'))
                                            ->searchable()
                                            ->createOptionForm(Rank::form())
                                            ->editOptionForm(Rank::form())
                                            ->columnSpan(1),
                                    ])
                                    ->columnSpan(1)->columns(2),

                                Forms\Components\Select::make('unit_id')
                                    ->relationship(name: 'unit', titleAttribute: 'name')
                                    ->label(__('Unit'))
                                    ->searchable()
                                    ->createOptionForm(Unit::form())
                                    ->editOptionForm(Unit::form())
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 2,
                                'lg' => 2,
                                'xl' => 2,
                            ]),
                    ])
                    ->columnSpanFull()
                    ->columns([
                        'sm' => 1,
                        'md' => 3,
                        'lg' => 3,
                        'xl' => 3,
                    ]),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\DatePicker::make('birth')
                            //->native(false)->displayFormat('Y-m-d')
                            ->nullable()->format('Y-m-d')->before('death')
                            ->label(__('Date of birth'))
                            ->columnSpan(1),
                        Forms\Components\Select::make('birth_location_id')
                            ->columnSpan(['sm' => 1, 'md' => 2, 'lg' => 2, 'xl' => 3])
                            ->label(__('Birth location'))
                            ->relationship(name: 'birthLocation', titleAttribute: 'name')
                            ->searchable()
                            ->createOptionForm(Location::form())
                            ->editOptionForm(Location::form()),
                        Forms\Components\DatePicker::make('death')
                            //->native(false)->displayFormat('Y-m-d')
                            ->nullable()->format('Y-m-d')->after('birth')
                            //->before('burial')
                            ->before(fn (Forms\Get $get) => filled($get('burial')) ? 'burial' : null)
                            //->rules(['nullable', 'date', 'after:birth', 'before:burial'])
                            //->rules('nullable|date|after:birth|before:burial')
                            ->label(__('Date of death'))
                            ->columnSpan(1),
                        Forms\Components\Select::make('death_location_id')
                            ->columnSpan(['sm' => 1, 'md' => 2, 'lg' => 2, 'xl' => 3])
                            ->label(__('Death location'))
                            ->searchable()
                            ->relationship(name: 'deathLocation', titleAttribute: 'name')
                            ->createOptionForm(Location::form())
                            ->editOptionForm(Location::form()),
                        Forms\Components\DatePicker::make('burial')
                            //->native(false)->displayFormat('Y-m-d')
                            ->nullable()->format('Y-m-d')->after('death')
                            //->rules(['nullable', 'date', 'after:death'])
                            //->rules('nullable|date|after:death')
                            ->label(__('Date of burial'))
                            ->columnSpan(1),
                        Forms\Components\Select::make('burial_location_id')
                            ->columnSpan(['sm' => 1, 'md' => 2, 'lg' => 2, 'xl' => 3])
                            ->label(__('Burial location'))
                            ->searchable()
                            ->relationship(name: 'burialLocation', titleAttribute: 'name')
                            ->createOptionForm(Location::form())
                            ->editOptionForm(Location::form()),
                        Forms\Components\DatePicker::make('wound')
                            //->native(false)->displayFormat('Y-m-d')
                            ->nullable()->format('Y-m-d')->before('death')->after('birth')
                            ->label(__('Date of wound'))
                            ->columnSpan(1),
                        Forms\Components\Select::make('wound_location_id')
                            ->columnSpan(['sm' => 1, 'md' => 2, 'lg' => 2, 'xl' => 3])
                            ->label(__('Wound location'))
                            ->searchable()
                            ->relationship(name: 'woundLocation', titleAttribute: 'name')
                            ->createOptionForm(Location::form())
                            ->editOptionForm(Location::form()),

                    ])->columnSpanFull()->columns([
                        'sm' => 1,
                        'md' => 3,
                        'lg' => 3,
                        'xl' => 4,
                    ]),

                Forms\Components\RichEditor::make('obituary')
                    ->label(__('Obituary'))
                    ->extraInputAttributes([
                        'style' => 'min-height: 500px;',
                    ])
                    ->columnSpanFull(),

                ...Images::form(Person::class, 'photos'),

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
                Tables\Columns\ImageColumn::make('photo')
                    ->translateLabel()
                    ->circular()
                    ->toggleable()
                    ->alignment(Alignment::Center),
                Tables\Columns\TextColumn::make('full_name')
                    ->label(__('Full name'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn (
                        Person $record
                    ): string => $record->full_name.($record->call_sign ? ' &laquo;'.$record->call_sign.'&raquo;' : ''))
                    ->html(),
                Tables\Columns\TextColumn::make('slug')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('unit.name')
                    ->label(__('Unit'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignment(Alignment::Center),
                Tables\Columns\TextColumn::make('rank.name')
                    ->label(__('Rank'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignment(Alignment::Center),
                Tables\Columns\TextColumn::make('militaryPosition.name')
                    ->label(__('Military position'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignment(Alignment::Center),
                Tables\Columns\TextColumn::make('slug')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('birth')
                    ->label(__('Date of birth'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignment(Alignment::Center),
                Tables\Columns\TextColumn::make('death')
                    ->label(__('Date of death'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignment(Alignment::Center),
                Tables\Columns\TextColumn::make('burial')
                    ->label(__('Date of burial'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignment(Alignment::Center),
                Tables\Columns\TextColumn::make('wound')
                    ->label(__('Date of wound'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignment(Alignment::Center),
                Tables\Columns\IconColumn::make('state')
                    ->translateLabel()
                    ->boolean()
                    ->action(function ($record, $column) {
                        $name = $column->getName();
                        $record->update([
                            $name => $record->$name === State::PUBLISHED ? State::UNPUBLISHED : State::PUBLISHED,
                        ]);
                    })
                    ->tooltip(fn ($state) => $state === State::PUBLISHED ? __('Unpublish') : __('Publish'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignment(Alignment::Center),
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
            ->emptyStateHeading(__('No people'))
            ->paginated([10, 25, 50, 100, 500])
            ->filters([

                Filter::date('birth'),
                Filter::date('death'),

                Tables\Filters\SelectFilter::make('state')
                    ->options(State::class)
                    ->label(__('State')),

                Tables\Filters\TrashedFilter::make(),

                Tables\Filters\SelectFilter::make('sex')
                    ->options(Sex::class)
                    ->label(__('Sex')),

                Tables\Filters\SelectFilter::make('citizenship')
                    ->options(Citizenship::class)
                    ->label(__('Citizenship')),

                Tables\Filters\TernaryFilter::make('civil')
                    ->label(__('Civil'))
                    ->options([
                        1 => __('Yes'),
                        0 => __('No'),
                    ]),

                Tables\Filters\SelectFilter::make('rank_id')
                    ->relationship('rank', 'name')
                    ->searchable()
                    ->label(__('Rank')),

                Tables\Filters\SelectFilter::make('military_position_id')
                    ->relationship('militaryPosition', 'name')
                    ->searchable()
                    ->label(__('Military position'))
                    ->columnSpan(2),

                Tables\Filters\SelectFilter::make('unit_id')
                    ->relationship('unit', 'name')
                    ->searchable()
                    ->label(__('Unit'))
                    ->columnSpan(2),

                Tables\Filters\SelectFilter::make('award')
                    ->relationship('awards', 'name')
                    ->searchable()
                    ->label(__('Award'))
                    ->columnSpan(2),

                ...PersonHelper::getLocationFilters(['birth', 'death', 'burial', 'wound']),

                Tables\Filters\SelectFilter::make('battle_id')
                    ->relationship('battles', 'name')
                    ->searchable()
                    ->label(__('Battle'))
                    ->columnSpan(2),

                Tables\Filters\SelectFilter::make('memorial_id')
                    ->relationship('memorials', 'name')
                    ->searchable()
                    ->label(__('Memorial'))
                    ->columnSpan(2),

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
                    Tables\Actions\BulkAction::make('publish')
                        ->label(__('Publish'))
                        ->color('success')
                        ->icon('heroicon-o-eye')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(fn (Collection $records) => $records->each(
                            fn (Person $person) => $person->update(['state' => State::PUBLISHED])
                        ))
                        ->visible(fn (Table $table) => ! Trashed::filtered($table)),
                    Tables\Actions\BulkAction::make('unpublish')
                        ->label(__('Unpublish'))
                        ->color('warning')
                        ->icon('heroicon-o-eye-slash')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(fn (Collection $records) => $records->each(
                            fn (Person $person) => $person->update(['state' => State::UNPUBLISHED])
                        ))
                        ->visible(fn (Table $table) => ! Trashed::filtered($table)),
                    Tables\Actions\DeleteBulkAction::make(),
                    ...Trashed::bulkActions(),
                ]),
            ])
            ->filtersFormColumns(4)
            ->filtersFormWidth(MaxWidth::FiveExtraLarge)
            ->recordClasses(fn (
                Person $record,
                Table $table
            ) => ! Trashed::filtered($table) && $record->trashed() ? 'bg-red-50' : '')
            ->selectCurrentPageOnly();
    }

    private static function trashedFiltered(Table $table): bool
    {
        return $table->getFiltersForm()->getState()['trashed']['value'] === '0';
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UnitsRelationManager::class,
            RelationManagers\MemorialsRelationManager::class,
            RelationManagers\AwardsRelationManager::class,
            RelationManagers\LinksRelationManager::class,
            AuditsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeople::route('/'),
            'create' => Pages\CreatePerson::route('/create'),
            'edit' => Pages\EditPerson::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('People');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPluralModelLabel(): string
    {
        return __('People');
    }
}
