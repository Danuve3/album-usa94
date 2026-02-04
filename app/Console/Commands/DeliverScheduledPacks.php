<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\PackDeliveryService;
use Illuminate\Console\Command;

class DeliverScheduledPacks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packs:deliver';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deliver pending packs to all users based on elapsed time';

    /**
     * Execute the console command.
     */
    public function handle(PackDeliveryService $packDeliveryService): int
    {
        $this->info('Starting pack delivery...');

        $users = User::where('is_banned', false)->get();
        $totalDelivered = 0;
        $usersReceived = 0;

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            $delivered = $packDeliveryService->deliverPendingPacks($user);

            if ($delivered > 0) {
                $totalDelivered += $delivered;
                $usersReceived++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Delivery complete: {$totalDelivered} packs delivered to {$usersReceived} users.");

        return Command::SUCCESS;
    }
}
