<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class MySqlBackUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mysql:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a MySQL Backup';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $username = \Config::get('database.connections.mysql.username');
        $password = \Config::get('database.connections.mysql.password');
        $dbname = \Config::get('database.connections.mysql.database');

        $filename = 'public/databasebackup/'.$dbname .'_backup_on_'. date('Y-m-d__h-i-s_a') . '.sql';

        //with password
        // exec('mysqldump -u ' .$username.' -p '.$password.' '.$dbname. ' > ' . $filename); 

        //without password
        exec('mysqldump -u' .$username.' '.$dbname. ' > ' . $filename);  
        
        $this->info('Your Backup is being saved to the directory ' . $filename);
    }
}
