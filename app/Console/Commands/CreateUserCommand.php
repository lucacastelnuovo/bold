<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Str;

use function Laravel\Prompts\info;
use function Laravel\Prompts\text;

class CreateUserCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-user {name} {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a user';

    public function handle()
    {
        User::create([
            'name'     => $this->argument('name'),
            'email'    => $this->argument('email'),
            'password' => Str::password(),
        ]);

        info('User created successfully.');
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'name' => fn () => text(
                label: 'Name',
                placeholder: 'E.g. Luca Castelnuovo',
                required: true,
                validate: 'unique:users,name',
            ),
            'email' => fn () => text(
                label: 'Email',
                placeholder: 'E.g. luca@castelnuovo.dev',
                required: true,
                validate: 'unique:users,email',
            ),
        ];
    }
}
