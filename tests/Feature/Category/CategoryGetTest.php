<?php

use App\Models\Category;
use App\Models\User;

test('category information can be received', function () {
    $user = User::factory()->create();
    $token = $user->createToken('api')->plainTextToken;
    $category = Category::factory()->create();

    $this->withToken($token)
        ->get("/api/categories/$category->id")
        ->assertStatus(200)
        ->assertJson($category->refresh()->toArray());
});

test('list of categories can be received', function () {
    $user = User::factory()->create();
    $token = $user->createToken('api')->plainTextToken;
    $categories = [];
    for ($i = 0; $i < 10; $i++) {
        $categories[] = Category::factory()
            ->create()
            ->refresh()
            ->toArray();
    }

    $this->withToken($token)
        ->get('/api/categories')
        ->assertStatus(200)
        ->assertJson($categories);
});
