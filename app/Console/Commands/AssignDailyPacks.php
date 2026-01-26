<?php

namespace App\Console\Commands;

use App\Models\Pack;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AssignDailyPacks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packs:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign daily packs to all active users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $packsPerDay = Setting::get('packs_per_day', 5);
        $usersProcessed = 0;
        $packsAssigned = 0;

        $users = User::all();

        foreach ($users as $user) {
            $packsToday = Pack::where('user_id', $user->id)->today()->count();

            if ($packsToday >= $packsPerDay) {
                continue;
            }

            $packsToAssign = $packsPerDay - $packsToday;

            for ($i = 0; $i < $packsToAssign; $i++) {
                Pack::create(['user_id' => $user->id]);
            }

            $usersProcessed++;
            $packsAssigned += $packsToAssign;
        }

        $message = "Daily packs assigned: {$usersProcessed} users processed, {$packsAssigned} packs assigned.";

        $this->info($message);
        Log::info($message);

        return Command::SUCCESS;
    }
}
