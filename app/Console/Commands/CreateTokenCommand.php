<?php

namespace App\Console\Commands;

use App\Enums\Ability;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\note;
use function Laravel\Prompts\search;
use function Laravel\Prompts\text;

class CreateTokenCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-token {user} {name} {abilities*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an token for a user';

    public function handle()
    {
        /** @var User */
        $user = User::findOrFail($this->argument('user'));

        $token = $user->createToken($this->argument('name'), $this->argument('abilities'));

        info('User token created successfully.');
        note($token->plainTextToken);
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'user' => fn () => search(
                label: 'User',
                placeholder: 'E.g. Luca Castelnuovo',
                options: fn ($value) => mb_strlen($value) > 0
                    ? User::where('name', 'like', "%{$value}%")->pluck('name', 'id')->all()
                    : []
            ),
            'name' => fn () => text(
                label: 'Name',
                placeholder: 'E.g. iPhone van Luca',
                required: true,
                validate: 'max:255',
            ),
            'abilities' => fn () => multiselect(
                label: 'Abilities',
                options: collect(Ability::cases())
                    ->mapWithKeys(fn ($ability) => [$ability->value => $ability->value])
                    ->all(),
                required: true,
            ),
        ];
    }
}
