<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:promote-user-to-admin {email}')]
#[Description('Grant administrator access to an existing user account')]
class PromoteUserToAdmin extends Command
{
    public function handle(): int
    {
        $user = User::query()->where('email', mb_strtolower(trim($this->argument('email'))))->first();

        if ($user === null) {
            $this->error('No user account exists with that email address.');

            return self::FAILURE;
        }

        $user->forceFill(['is_admin' => true])->save();
        $this->info("Administrator access granted to {$user->email}.");

        return self::SUCCESS;
    }
}
