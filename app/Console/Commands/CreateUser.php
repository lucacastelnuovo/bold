<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function Laravel\Prompts\info;
use function Laravel\Prompts\table;
use function Laravel\Prompts\text;

class CreateUser extends Command
{
    protected $signature = 'app:create-user {--name=} {--email=} {--password=}';

    protected $description = 'Create a user';

    public function handle(): int
    {
        $this->newLine();

        DB::transaction(function () {
            $name = $this->option('name') ?? text(
                label: 'What is the users name?',
                required: true
            );

            $email = $this->option('email') ?? text(
                label: 'What is the users email?',
                required: true
            );

            $password = $this->option('password') ?? Str::random(32);

            User::create([
                'name'     => $name,
                'email'    => $email,
                'password' => $password,
            ]);

            info('User created successfully!');
            table(
                ['Email', 'Password'],
                [[$email, $password]]
            );
        });

        return Command::SUCCESS;
    }
}
