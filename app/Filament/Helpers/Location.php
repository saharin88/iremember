<?php

namespace App\Filament\Helpers;

use App\Filament\Resources\PersonResource;
use App\Models\Location as Model;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Support\Enums\Alignment;
use Filament\Tables;

final class Location
{
    public static function form(bool $withImages = false): array
    {
        $schema = [
            Forms\Components\TextInput::make('name')
                ->label(__('Name'))
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('slug')
                ->unique(ignoreRecord: true)
                ->rule('alpha_dash:ascii')
                ->placeholder(__('Will be generated automatically from full name')),
            Forms\Components\RichEditor::make('description')
                ->label(__('Description'))
                ->columnSpanFull(),
            Forms\Components\Select::make('parent_id')
                ->label(__('Parent'))
                ->searchable()
                ->relationship('parent', 'name')
                ->placeholder(__('Select parent location')),
            Forms\Components\Group::make()
                ->extraAttributes(['class' => 'mt-[38px]'])
                ->schema([
                    Forms\Components\Toggle::make('is_administrative_division')
                        ->formatStateUsing(fn (Get $get) => ! empty($get('koatuu')) || ! empty($get('katottg')))
                        ->translateLabel()
                        ->live(),
                ]),
            Forms\Components\Group::make()
                ->schema([
                    Forms\Components\TextInput::make('koatuu')
                        ->label(__('KOATUU'))
                        ->hintIcon('heroicon-m-question-mark-circle',
                            tooltip: __('Classification of objects of the administrative-territorial system of Ukraine'))
                        ->disabled(fn (Get $get) => ! $get('is_administrative_division'))
                        ->rules('nullable|digits:10')->mask('9999999999'),
                    Forms\Components\TextInput::make('katottg')
                        ->label(__('KATOTTG'))
                        ->hintIcon('heroicon-m-question-mark-circle',
                            tooltip: __('Codifier of administrative-territorial units and territories of territorial communities'))
                        ->rules('nullable|size:19')->mask('UA99999999999999999')
                        ->disabled(fn (Get $get) => ! $get('is_administrative_division')),
                    Forms\Components\TextInput::make('lat')
                        ->rules('nullable|between:-90,90')
                        ->label(__('Latitude'))
                        ->disabled(fn (Get $get) => $get('is_administrative_division'))
                        ->numeric(),
                    Forms\Components\TextInput::make('lng')
                        ->rules('nullable|between:-180,180')
                        ->label(__('Longitude'))
                        ->disabled(fn (Get $get) => $get('is_administrative_division'))
                        ->numeric(),
                ])
                ->columns(4)
                ->columnSpanFull(),
        ];

        return $withImages ? array_merge($schema, Images::form(Model::class)) : $schema;
    }

    public static function peopleCountColumns(array|string $relations): array
    {
        $columns = [];
        if (is_array($relations)) {
            foreach ($relations as $relation) {
                $columns = array_merge($columns, self::peopleCountColumns($relation));
            }
        } else {
            $columns[] = Tables\Columns\TextColumn::make("people_{$relations}_count")
                ->counts('people'.ucfirst($relations))
                ->url(fn ($record): string => PersonResource::getUrl('index',
                    ["tableFilters[{$relations}_location_id][value]" => $record->id]))
                ->color('primary')
                ->label(fn () => match ($relations) {
                    'birth' => __('Born'),
                    'death' => __('Died'),
                    'burial' => __('Buried'),
                    'wound' => __('Wounded'),
                })
                ->alignment(Alignment::Center)
                ->sortable()
                ->toggleable();
        }

        return $columns;
    }
}
