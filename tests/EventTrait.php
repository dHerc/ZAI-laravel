<?php

namespace Tests;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Support\Collection;

trait EventTrait
{
    private function getEventMockData(): Collection
    {
        $data = collect(Event::factory()->definition());
        $category = Category::factory()->create();
        $data['category_id'] = $category->id;
        $data['start_date'] = $data['start_date']->format('Y-m-d');
        $data['end_date'] = $data['end_date']->format('Y-m-d');
        return $data;
    }

    private function assertEventData(Event $event, Collection|array $data): void
    {
        $this->assertTrue($event !== null);
        $this->assertEquals($data['name'], $event->name);
        $this->assertEquals($data['start_date'], $event->start_date);
        $this->assertEquals($data['end_date'], $event->end_date);
        $this->assertEquals($data['category_id'], $event->category->id);
        $this->assertEquals($data['description'], $event->description);
    }
}
