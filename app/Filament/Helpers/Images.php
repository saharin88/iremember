<?php

namespace App\Filament\Helpers;

use Filament\Forms;
use Filament\Forms\Components\Component;
use Illuminate\Support\Str;

final class Images
{
    public static function form(string $modelClass, string $relationship = 'images'): array
    {
        return [
            Forms\Components\Repeater::make($relationship)
                ->hiddenLabel()
                ->addActionLabel(__('Add '.Str::singular($relationship)))
                ->relationship(name: $relationship)
                ->schema([
                    Forms\Components\FileUpload::make('path')
                        ->label(__(ucfirst(Str::singular($relationship))))
                        ->required()
                        ->directory(function (Component $component) use ($modelClass) {
                            $directory = ['images'];
                            $directory[] = Str::plural(Str::kebab(class_basename($modelClass)));
                            $directory[] = $component?->getParentRepeater()?->getRecord()?->id ?? $modelClass::max('id') + 1;

                            return implode('/', $directory);
                        })
                        ->imagePreviewHeight('250')
                        ->image()
                        ->imageEditor()
                        ->openable(),
                    Forms\Components\TextInput::make('name')->label(__('Name')),
                    Forms\Components\Textarea::make('description')->label(__('Description')),
                ])
                ->columnSpanFull()
                ->defaultItems(0)
                ->grid([
                    'default' => 1,
                    'sm' => 2,
                    'md' => 2,
                    'lg' => 2,
                    'xl' => 3,
                ]),
        ];
    }
}
