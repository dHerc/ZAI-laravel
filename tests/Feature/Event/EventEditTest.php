<?php

use App\Models\Category;
use App\Models\Event;
use App\Models\Image;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\EventTrait;

uses(EventTrait::class);

test('event can be added without photo', function () {
    $user = User::factory()->create();
    $token = $user->createToken('api')->plainTextToken;
    $data = $this->getEventMockData();

    $response = $this->withToken($token)
        ->post('/api/events', $data->toArray());

    $response->assertStatus(201)
        ->assertJson($data->except('category_id')->toArray());

    $id = $response->json('id');
    /** @var Event $event */
    $event = Event::query()->find($id);
    $this->assertEventData($event, $data);
});

test('event can be added with photo', function () {
    Storage::fake('images');
    $user = User::factory()->create();
    $token = $user->createToken('api')->plainTextToken;
    Category::factory()->create();
    $data = $this->getEventMockData();
    $file = UploadedFile::fake()->image('file.jpg');
    $data['image'] = $file;

    $response = $this->withToken($token)
        ->post('/api/events', $data->toArray());

    $response->assertStatus(201)
        ->assertJson($data->except(['category_id', 'image'])->toArray());

    $id = $response->json('id');
    /** @var Event $event */
    $event = Event::query()->find($id);
    $this->assertEventData($event, $data);
    Storage::disk('images')->assertExists($event->image->filename);
});

test('event can be edited without photo', function () {
    $user = User::factory()->create();
    $token = $user->createToken('api')->plainTextToken;
    $category = Category::factory()->create();
    $event = Event::factory()->create(['category_id' => $category->id]);
    $data = $this->getEventMockData();

    $response = $this->withToken($token)
        ->patch("/api/events/$event->id", $data->toArray());

    $response->assertStatus(200)
        ->assertJson($data->except('category_id')->toArray());

    $this->assertEventData($event->refresh(), $data);
});

test('event data will not change if data sent was empty', function () {
    $user = User::factory()->create();
    $token = $user->createToken('api')->plainTextToken;
    $category = Category::factory()->create();
    $event = Event::factory()->create(['category_id' => $category->id]);
    $image = Image::factory()->create(['event_id' => $event->id]);
    $eventData = collect($event->refresh());
    $eventData['category_id'] = $category->id;

    $response = $this->withToken($token)
        ->patch("/api/events/$event->id");

    $response->assertStatus(200)
        ->assertJson($eventData->except(['category_id'])->toArray());

    $this->assertEventData($event->refresh(), $eventData);
    $this->assertEquals($event->image->id, $image->id);
});

test('event image will be removed if image is sent and is empty', function () {
    $user = User::factory()->create();
    $token = $user->createToken('api')->plainTextToken;
    $category = Category::factory()->create();
    $event = Event::factory()->create(['category_id' => $category->id]);
    Image::factory()->create(['event_id' => $event->id]);

    $response = $this->withToken($token)
        ->patch("/api/events/$event->id", ['image' => '']);

    $response->assertStatus(200)
        ->assertJsonPath('image', null);
});

test('event can be edited with photo', function () {
    Storage::fake('images');
    $user = User::factory()->create();
    $token = $user->createToken('api')->plainTextToken;
    $category = Category::factory()->create();
    $event = Event::factory()->create(['category_id' => $category->id]);
    $image = Image::factory()->create(['event_id' => $event->id]);
    $newCategory = Category::factory()->create();
    $data = $this->getEventMockData();
    $data['category_id'] = $newCategory->id;
    $file = UploadedFile::fake()->image('file.jpg');
    $data['image'] = $file;

    $response = $this->withToken($token)
        ->post("/api/events/$event->id", [...$data->toArray(), '_method' => 'patch']);

    $response->assertStatus(200)
        ->assertJson($data->except(['category_id', 'image'])->toArray());

    $event->refresh();
    $this->assertEventData($event, $data);
    $this->assertNotEquals($event->image->id, $image->id);
    Storage::disk('images')->assertExists($event->image->filename);
});

test('event can be removed', function () {
    $user = User::factory()->create();
    $token = $user->createToken('api')->plainTextToken;
    $category = Category::factory()->create();
    $event = Event::factory()->create(['category_id' => $category->id]);
    Image::factory()->create(['event_id' => $event->id]);
    $response = $this->withToken($token)
        ->delete("/api/events/$event->id");

    $response->assertStatus(200);
    $this->assertTrue($event->refresh()->trashed());
});
