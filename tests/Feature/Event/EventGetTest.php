<?php

use App\Models\Category;
use App\Models\Event;
use App\Models\User;

test('event information can be retrieved', function () {
    $user = User::factory()->create();
    $token = $user->createToken('api')->plainTextToken;
    $category = Category::factory()->create();
    $event = Event::factory()->create(['category_id' => $category->id]);

    $this->withToken($token)
        ->get("/api/events/$event->id")
        ->assertStatus(200)
        ->assertJson($event->refresh()->toArray());
});

test('list of events can be retrieved', function () {
    $user = User::factory()->create();
    $token = $user->createToken('api')->plainTextToken;
    $category = Category::factory()->create();
    $events = [];
    for ($i = 0; $i < 10; $i++) {
        $events[] = Event::factory()->create([
            'category_id' => $category->id
        ])->refresh()->toArray();
    }

    $this->withToken($token)
        ->get('/api/events')
        ->assertStatus(200)
        ->assertJson($events);
});

test('parameters work properly in start mode', function (?string $dateFrom, ?string $dateTo) {
    $user = User::factory()->create();
    $token = $user->createToken('api')->plainTextToken;
    $category = Category::factory()->create();
    $events = collect();
    for ($day = 10; $day < 20; $day++) {
        $startDate = "2020-10-$day";
        $endDate = "2022-10-30";
        $events->add(Event::factory()->create(
            ['start_date' => $startDate, 'end_date' => $endDate, 'category_id' => $category->id]
        )->refresh());
    }
    $this->withToken($token)
        ->get('/api/events')
        ->assertStatus(200)
        ->assertJson($events->filter(function ($event) use ($dateFrom, $dateTo) {
            return ($event['start_date'] >= $dateFrom ?? '2020-10-01') &&
                ($event['start_date'] <= $dateTo ?? '2020-10-30');
        })->toArray());
})->with([
    ['2020-10-01', '2020-10-30'],
    ['2020-10-15', '2020-10-30'],
    ['2020-10-01', '2020-10-15'],
    ['2020-10-13', '2020-10-17'],
    ['2020-10-15', null],
    [null, '2020-10-15']
]);

test('parameters work properly in end mode', function (?string $dateFrom, ?string $dateTo) {
    $user = User::factory()->create();
    $token = $user->createToken('api')->plainTextToken;
    $category = Category::factory()->create();
    $events = collect();
    for ($day = 10; $day < 20; $day++) {
        $startDate = "2020-10-01";
        $endDate = "2022-10-$day";
        $events->add(Event::factory()->create(
            ['start_date' => $startDate, 'end_date' => $endDate, 'category_id' => $category->id]
        )->refresh());
    }
    $this->withToken($token)
        ->get('/api/events')
        ->assertStatus(200)
        ->assertJson($events->filter(function ($event) use ($dateFrom, $dateTo) {
            return ($event['end_date'] >= $dateFrom ?? '2020-10-01') &&
                ($event['end_date'] <= $dateTo ?? '2020-10-30');
        })->toArray());
})->with([
    ['2020-10-01', '2020-10-30'],
    ['2020-10-15', '2020-10-30'],
    ['2020-10-01', '2020-10-15'],
    ['2020-10-13', '2020-10-17'],
    ['2020-10-15', null],
    [null, '2020-10-15']
]);

test('parameters work properly in any mode', function (?string $dateFrom, ?string $dateTo) {
    $user = User::factory()->create();
    $token = $user->createToken('api')->plainTextToken;
    $category = Category::factory()->create();
    $events = collect();
    for ($i = 0; $i < 10; $i++) {
        $day = $i + 10;
        $startDate = "2020-10-$day";
        $endDay = $day + $i;
        $endDate = "2022-10-$endDay";
        $events->add(Event::factory()->create(
            ['start_date' => $startDate, 'end_date' => $endDate, 'category_id' => $category->id]
        )->refresh());
    }
    $this->withToken($token)
        ->get('/api/events')
        ->assertStatus(200)
        ->assertJson($events->filter(function ($event) use ($dateFrom, $dateTo) {
            if ($event['start_date'] < $dateFrom && $event['end_date'] > $dateTo) {
                return true;
            }
            return ($event['end_date'] >= $dateFrom ?? '2020-10-01') &&
                ($event['start_date'] <= $dateTo ?? '2020-10-30');
        })->toArray());
})->with([
    ['2020-10-01', '2020-10-30'],
    ['2020-10-15', '2020-10-30'],
    ['2020-10-01', '2020-10-15'],
    ['2020-10-13', '2020-10-17'],
    ['2020-10-15', null],
    [null, '2020-10-15']
]);
