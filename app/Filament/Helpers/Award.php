<?php

namespace App\Filament\Helpers;

use App\Models\Award as Model;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Filament\Forms;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

final class Award
{
    public static function form(bool $withImages = false): array
    {
        $schema = [
            Forms\Components\Group::make()
                ->schema([

                    Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->translateLabel()
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('slug')
                                ->unique(ignoreRecord: true)
                                ->rule('alpha_dash:ascii')
                                ->placeholder(__('Will be generated automatically from name')),
                        ])
                        ->columnSpan([
                            'sm' => 1,
                            'md' => 2,
                            'lg' => 2,
                            'xl' => 2,
                        ]),

                    Forms\Components\FileUpload::make('image')
                        ->label(__('Main image'))
                        ->directory('awards')
                        ->getUploadedFileNameForStorageUsing(function (
                            TemporaryUploadedFile $file,
                            Forms\Get $get,
                        ) {
                            $slug = $get('slug') ?: SlugService::createSlug(\App\Models\Award::class, 'slug',
                                $get('name'));

                            return $slug.'.'.$file->guessExtension();
                        })
                        ->imagePreviewHeight('125')
                        ->openable()
                        ->image()
                        ->imageEditor(),

                    Forms\Components\RichEditor::make('description')
                        ->translateLabel()
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

        return $withImages ? array_merge($schema, Images::form(Model::class)) : $schema;
    }
}
