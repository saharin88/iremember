<?php

namespace App\Observers;

use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class ImageObserver
{
    public function deleted(Image $image): void
    {
        $imageableType = strtolower(class_basename($image->imageable_type));
        Storage::delete("public/images/{$imageableType}/{$image->imageable_id}/{$image->path}");
    }
}
