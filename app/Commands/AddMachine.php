<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;

class AddMachine extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new machine to connect to';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $description = $this->ask('Enter a description for the machine');
        $username = $this->ask('Enter the username used to connect to the machine');
        $ip_address = $this->ask('Enter the IP address of the machine');
        $private_key_path = $this->askForPrivateKeyPath();

        $this->info('Adding machine...');

        $machine = [
            'description' => $description,
            'ip_address' => $ip_address,
            'private_key_path' => $private_key_path,
        ];
        DB::table('machines')->insert($machine);

        $this->info('Machine added successfully');

        $this->info('Adding user...');

        $user = [
            'name' => $username,
            'machine_id' => DB::getPdo()->lastInsertId(),
        ];
        DB::table('users')->insert($user);

        $this->info('User added successfully');

        $this->info('Machine and user added successfully');
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }

    private function askForPrivateKeyPath(): string
    {
        $home = getenv('HOME');
        $keys = collect(glob("$home/.ssh/*"));

        $keys = $keys->filter(function ($key) {
            if (is_dir($key)) {
                return false;
            }

            if (strpos($key, '.pub') !== false) {
                return false;
            }

            if (strpos($key, 'config') !== false) {
                return false;
            }

            return true;
        });

        $options = $keys->map(function ($key) {
            return basename($key);
        })->toArray();

        $menu = $this->menu('Select a private key to use', $options)
            ->setForegroundColour('white')
            ->setBackgroundColour('black');

        $selectedKey = $menu->open();
        if ($selectedKey === null) {
            $this->info('No key selected. Exiting...');
            exit;
        }

        $private_key_path = $keys[$selectedKey];
        return $private_key_path;
    }
}
