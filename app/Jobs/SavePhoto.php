<?php

namespace App\Jobs;

use App\Models\Person;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class SavePhoto implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private Person $person,
        private string $url,
        private readonly bool $force = false
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $ext = pathinfo($this->url, PATHINFO_EXTENSION);
        $path = 'persons/'.$this->person->slug.'.'.$ext;
        if ($this->force || Storage::disk('public')->exists($path) === false) {
            $photo = file_get_contents($this->url);
            Storage::disk('public')->put($path, $photo);
        }
        $this->person->photo = $path;
        $this->person->save();
    }
}
