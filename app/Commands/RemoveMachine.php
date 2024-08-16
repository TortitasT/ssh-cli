<?php

namespace App\Commands;

use App\Constants;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;

class RemoveMachine extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove a machine to connect to';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $machines = DB::table('machines')->orderByDesc('updated_at')->get();
        $options = $machines->map(function ($machine) {
            $description = $machine->description;
            $ip_address = $machine->ip_address;

            return "$description ($ip_address)";
        })->toArray();

        $title = Constants::HEADER . "\nSelect a machine to remove:";
        $menu = $this->menu($title, $options)
            ->setForegroundColour('white')
            ->setBackgroundColour('black');

        $selectedMachine = $menu->open();
        if ($selectedMachine === null) {
            $this->info('No machine selected. Exiting...');
            return;
        }

        $machine = $machines[$selectedMachine];

        $confirm = $this->confirm("Are you sure you want to remove the machine {$machine->description} ($machine->ip_address)?");
        if (!$confirm) {
            $this->info('Machine not removed. Exiting...');
            return;
        }

        DB::table('machines')->where('id', $machine->id)->delete();
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
