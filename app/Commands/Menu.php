<?php

namespace App\Commands;

use App\Constants;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Menu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:menu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shows a menu with all available commands';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $commands = [
            [
                'title' => 'List all machines',
                'command' => ListMachines::class,
            ],
            [
                'title' => 'Add a new machine',
                'command' => AddMachine::class,
            ],
            [
                'title' => 'Remove a machine',
                'command' => RemoveMachine::class,
            ],
        ];

        $title = Constants::HEADER . "\nSelect an option:";
        $options = array_column($commands, 'title');
        $selectedCommand = $this->menu($title, $options)
                                ->setForegroundColour('white')
                                ->setBackgroundColour('black')
                                ->open();

        if ($selectedCommand === null) {
            $this->info('No option selected. Exiting...');
            return;
        }

        $this->call($commands[$selectedCommand]['command']);
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
