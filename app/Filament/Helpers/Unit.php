<?php

namespace App\Filament\Helpers;

use Cviebrock\EloquentSluggable\Services\SlugService;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

final class Unit
{
    public static function form(bool $withImages = false): array
    {
        $schema = [
            Forms\Components\Group::make()
                ->schema([

                    Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label(__('Name'))
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('slug')
                                ->unique(ignoreRecord: true)
                                ->rule('alpha_dash:ascii')
                                ->placeholder(__('Will be generated automatically from full name')),
                            Forms\Components\Textarea::make('full_name')
                                ->label(__('Full title'))
                                ->maxLength(65535)
                                ->columnSpanFull(),
                            Forms\Components\Group::make()
                                ->schema([
                                    Forms\Components\Select::make('parent_id')
                                        ->label(__('Parent'))
                                        ->searchable()
                                        ->relationship(
                                            name: 'parent',
                                            titleAttribute: 'name',
                                            modifyQueryUsing: fn (
                                                Builder $query,
                                                $record
                                            ) => $record?->id ? $query->where('id',
                                                '!=',
                                                $record->id) : $query
                                        ),
                                    Forms\Components\Select::make('military_branch_id')
                                        ->label(__('Military branch'))
                                        ->relationship('militaryBranch', 'name'),
                                ])->columns([
                                    'lg' => 1,
                                    'xl' => 2,
                                ]),
                        ])
                        ->columnSpan([
                            'sm' => 1,
                            'md' => 2,
                            'lg' => 2,
                            'xl' => 2,
                        ]),

                    Forms\Components\FileUpload::make('image')
                        ->label(__('Main image'))
                        ->directory('units')
                        ->getUploadedFileNameForStorageUsing(function (
                            TemporaryUploadedFile $file,
                            Forms\Get $get,
                        ) {
                            $slug = $get('slug') ?: SlugService::createSlug(\App\Models\Unit::class, 'slug',
                                $get('name'));

                            return $slug.'.'.$file->guessExtension();
                        })
                        ->imagePreviewHeight('335')
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
        ];

        return $withImages ? array_merge($schema, Images::form(\App\Models\Unit::class)) : $schema;
    }
}
