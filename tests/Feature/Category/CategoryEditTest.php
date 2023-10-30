<?php

use App\Models\Category;
use App\Models\User;

test('category can be added', function () {
    $user = User::factory()->create();
    $token = $user->createToken('api')->plainTextToken;
    $data = Category::factory()->definition();

    $response = $this->withToken($token)
        ->post('/api/categories', $data);

    $response->assertStatus(201)
        ->assertJson($data);

    $id = $response->json('id');
    /** @var Category $category */
    $category = Category::query()->find($id);
    $this->assertEqualsCanonicalizing(
        $data,
        collect($category)->only(array_keys($data))->toArray()
    );
});

test('category can be edited', function () {
    $user = User::factory()->create();
    $token = $user->createToken('api')->plainTextToken;
    $category = Category::factory()->create();
    $data = Category::factory()->definition();

    $response = $this->withToken($token)
        ->patch("/api/categories/$category->id", $data);

    $response->assertStatus(200)
        ->assertJson($data);

    $this->assertEqualsCanonicalizing(
        $data,
        collect($category->refresh())->only(array_keys($data))->toArray()
    );
});

test('category data will not change if data sent was empty', function () {
    $user = User::factory()->create();
    $token = $user->createToken('api')->plainTextToken;
    $category = Category::factory()->create();
    $categoryData = $category->toArray();

    $response = $this->withToken($token)
        ->patch("/api/categories/$category->id");

    $response->assertStatus(200)
        ->assertJson($categoryData);

    $this->assertEqualsCanonicalizing(
        $categoryData,
        collect($category->refresh())->only(array_keys($categoryData))->toArray()
    );
});

test('category can be removed', function () {
    $user = User::factory()->create();
    $token = $user->createToken('api')->plainTextToken;
    $category = Category::factory()->create();
    $response = $this->withToken($token)
        ->delete("/api/categories/$category->id");

    $response->assertStatus(200);
    $this->assertTrue($category->refresh()->trashed());
});
