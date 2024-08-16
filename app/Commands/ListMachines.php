<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Process\Process;

class ListMachines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all machines available to connect to';

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

        $menu = $this->menu('Select a machine to connect to', $options)
            ->setForegroundColour('white')
            ->setBackgroundColour('black');
        $selectedMachine = $menu->open();
        if ($selectedMachine === null) {
            $this->info('No machine selected. Exiting...');
            return;
        }

        $users = DB::table('users')->where('machine_id', $machines[$selectedMachine]->id)->get();
        $username = null;

        if ($users->isEmpty()) {
            $newUserName = $this->ask('Enter the username used to connect to the machine');

            $this->info('Adding user...');

            $user = [
                'name' => $newUserName,
                'machine_id' => $machines[$selectedMachine]->id,
            ];
            DB::table('users')->insert($user);

            $this->info('User added successfully');

            $username = $newUserName;
        } elseif ($users->count() === 1) {
            $username = $users->first()->name;
        } else {
            $menu = $this->menu('Select a user to connect as', $users->pluck('name')->toArray())
                ->setForegroundColour('white')
                ->setBackgroundColour('black');
            $selectedUser = $menu->open();
            if ($selectedUser === null) {
                $this->info('No user selected. Exiting...');
                return;
            }

            $username = $users[$selectedUser]->name;
            if ($selectedUser === null) {
                $this->info('No user selected. Exiting...');
                return;
            }
        }

        $this->info("Connecting to {$machines[$selectedMachine]->description}...");
        Process::fromShellCommandline('ssh "${:USERNAME}"@"${:HOST}" -i "${:PRIVATE_KEY_PATH}"', getenv('HOME') . '/.ssh')
            ->setTty(true)
            ->run(null, [
                'USERNAME' => $username,
                'HOST' => $machines[$selectedMachine]->ip_address,
                'PRIVATE_KEY_PATH' => $machines[$selectedMachine]->private_key_path,
            ]);
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
