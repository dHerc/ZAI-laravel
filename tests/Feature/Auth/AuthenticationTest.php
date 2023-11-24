<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;

test('users can authenticate through api', function () {
    $user = User::factory()->create();

    $response = $this->post('/api/profile/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertJsonStructure(['token']);
});

test('users can not authenticate through api with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/api/profile/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertStatus(422);
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
