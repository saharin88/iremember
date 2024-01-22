<?php

namespace App\Console\Commands;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CreateUserCommand extends Command
{
    protected $signature = 'users:create';

    protected $description = 'Creates a new user';

    public function handle()
    {
        $user['name'] = $this->ask('Name of the new user');
        $user['email'] = $this->ask('Email of the new user');
        $user['password'] = $this->secret('Password of the new user');

        $choices = array_column(Role::cases(), 'name', 'value');
        $roleName = $this->choice('Role of the new user', $choices, Role::ADMINISTRATOR->value);
        $user['role'] = array_search($roleName, $choices);

        $validator = Validator::make($user, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', Password::defaults()],
            'role' => [Rule::enum(Role::class)],
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return -1;
        }

        $user['password'] = Hash::make($user['password']);
        User::create($user);

        $this->info('User created successfully.');

        return 0;
    }
}
