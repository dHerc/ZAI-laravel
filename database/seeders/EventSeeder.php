<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Event;
use App\Models\Image;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Event::query()->delete();
        $categories = Category::all();
        foreach ($categories as $category) {
            for ($i = 0; $i < 3; $i++) {
                Event::factory()->create(['category_id' => $category->id]);
            }
        }
    }
}
