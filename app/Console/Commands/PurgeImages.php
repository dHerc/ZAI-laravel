<?php

namespace App\Console\Commands;

use App\Models\Image;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PurgeImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:purge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes images without database records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $files = collect(Storage::disk('images')->files());
        $db = Image::query()->pluck('filename');
        $toRemove = $files->diff($db)->filter(fn($file) => !str_starts_with($file, '.'));
        foreach ($toRemove as $file) {
            Storage::disk('images')->delete($file);
        }
    }
}
