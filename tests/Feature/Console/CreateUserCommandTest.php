<?php

use App\Enums\Role;
use App\Models\User;

it('creates a user', function () {
    $this->artisan('users:create')
        ->expectsQuestion('Name of the new user', 'Test User')
        ->expectsQuestion('Email of the new user', 'test@example.com')
        ->expectsQuestion('Password of the new user', 'password')
        ->expectsQuestion('Role of the new user', Role::ADMINISTRATOR->name)
        ->expectsOutput('User created successfully.')
        ->assertSuccessful();
    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
});

it('fails to create a user with invalid data', function () {
    $this->artisan('users:create')
        ->expectsQuestion('Name of the new user', '')
        ->expectsQuestion('Email of the new user', 'test@example.com')
        ->expectsQuestion('Password of the new user', '')
        ->expectsQuestion('Role of the new user', Role::ADMINISTRATOR->name)
        ->assertFailed();
    $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
});

it('fails to create a user with exist email', function () {
    User::factory()->create(['email' => 'test@example.com']);
    $this->artisan('users:create')
        ->expectsQuestion('Name of the new user', 'Test User')
        ->expectsQuestion('Email of the new user', 'test@example.com')
        ->expectsQuestion('Password of the new user', 'password')
        ->expectsQuestion('Role of the new user', Role::ADMINISTRATOR->name)
        ->assertFailed();
});
