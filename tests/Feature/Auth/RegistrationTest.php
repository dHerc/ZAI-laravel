<?php

test('new users can register thru api', function () {
    $response = $this->post('/api/profile/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertJsonStructure(['token']);
});
