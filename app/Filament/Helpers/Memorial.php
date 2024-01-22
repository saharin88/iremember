<?php

namespace App\Filament\Helpers;

use Cviebrock\EloquentSluggable\Services\SlugService;
use Filament\Forms;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

final class Memorial
{
    public static function form(bool $withImages = false): array
    {
        $schema = [
            Forms\Components\Group::make()
                ->schema([

                    Forms\Components\Group::make([
                        Forms\Components\TextInput::make('name')
                            ->translateLabel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->unique(ignoreRecord: true)
                            ->rule('alpha_dash:ascii')
                            ->placeholder(__('Will be generated automatically from name')),
                        Forms\Components\Select::make('location_id')
                            ->label(__('Location'))
                            ->relationship('location', 'name')
                            ->searchable()
                            ->createOptionForm(Location::form())
                            ->editOptionForm(Location::form()),
                        Forms\Components\DatePicker::make('date')
                            ->translateLabel(),
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
        ];

        return $withImages ? array_merge($schema, Images::form(\App\Models\Memorial::class)) : $schema;
    }
}
