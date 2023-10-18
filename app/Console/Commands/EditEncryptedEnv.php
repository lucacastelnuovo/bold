<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\warning;

class EditEncryptedEnv extends Command
{
    protected $signature = 'env:edit {--c|code : Open the decrypted .env file in VS Code}';

    protected $description = 'Decrypt, Edit and Encrypt .env file';

    public function handle()
    {
        $disk = Storage::build(['driver' => 'local', 'root' => base_path()]);

        $encryptedEnvs = collect($disk->files())
            ->filter(fn (string $value) => Str::startsWith($value, '.env') && Str::endsWith($value, '.encrypted'));

        $selectedEnv = select(
            label: 'Which encrypted .env file do you want to edit?',
            options: $encryptedEnvs,
        );

        $encryptedEnv = $encryptedEnvs[$selectedEnv];
        $decryptedEnv = Str::replaceLast('.encrypted', '', $encryptedEnv);

        $key = password(
            label: "What is the decryption key for {$encryptedEnv}?",
            required: true,
        );

        if (Command::SUCCESS !== $this->call('env:decrypt', [
            '--key'   => $key,
            '--env'   => $this->env($encryptedEnv),
            '--force' => true,
            '--quiet' => true,
        ])) {
            error("{$encryptedEnv} could not be decrypted");

            return Command::FAILURE;
        }

        info("Please edit {$decryptedEnv} and save it.");
        note($disk->path($decryptedEnv));

        if ($this->option('code')) {
            Process::quietly()
                ->forever()
                ->run("code --wait {$disk->path($decryptedEnv)}");
        }

        $storeChanges = confirm(
            label: "Store changes made to {$decryptedEnv}?",
            default: false,
        );

        if ($storeChanges) {
            if (Command::SUCCESS !== $this->call('env:encrypt', [
                '--key'   => $key,
                '--env'   => $this->env($decryptedEnv),
                '--force' => true,
                '--quiet' => true,
            ])) {
                error("{$decryptedEnv} could not be encrypted");

                return Command::FAILURE;
            }

            info('Changes were saved and encrypted!');
        } else {
            warning('Changes were discarded!');
        }

        $disk->delete($decryptedEnv);

        return Command::SUCCESS;
    }

    protected function env(string $envFile): string
    {
        return str($envFile)
            ->replaceFirst('.env.', '')
            ->replaceLast('.encrypted', '');
    }
}
