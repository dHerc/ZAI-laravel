<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Image;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Image::query()->delete();
        $events = Event::all();
        foreach ($events as $event) {
            Image::factory()->create(['event_id' => $event->id]);
        }
    }
}
