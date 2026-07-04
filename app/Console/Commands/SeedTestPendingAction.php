<?php

namespace App\Console\Commands;

use App\Actions\PendingActions\TestPendingTaskAction;
use App\Models\User;
use Illuminate\Console\Command;

class SeedTestPendingAction extends Command
{
    /**
     * @var string
     */
    protected $signature = 'pending-actions:seed-test {email? : Email of the user to seed the task for (defaults to the first user)}';

    /**
     * @var string
     */
    protected $description = 'Seed a sample pending task for testing the Pending Actions screen.';

    public function handle(TestPendingTaskAction $action): int
    {
        $email = $this->argument('email');

        $user = $email !== null
            ? User::query()->where('email', $email)->first()
            : User::query()->first();

        if ($user === null) {
            $this->error($email !== null
                ? "No user found with email [{$email}]."
                : 'No users found to seed a pending task for.');

            return self::FAILURE;
        }

        $task = $action->handle($user);

        $this->info("Seeded pending task #{$task->getKey()} for {$user->email}.");

        return self::SUCCESS;
    }
}
