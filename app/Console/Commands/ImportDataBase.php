<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportDataBase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mysql:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Database from .sql file';

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


        //keeping the recent database back_up
        $filename = 'public/databasebackup/'.$dbname .'_'. date('Y-m-d_h_i_s') . '.sql';
                //with password
        // exec('mysqldump -u' .$username.' -p '.$password.' '.$dbname. ' > ' . $filename); 
                //without password
        exec('mysqldump -u' .$username.' '.$dbname. ' > ' . $filename);  
        $this->info('Your Backup is being saved to the directory ' . $filename);

        //--------Import Section------///
        // $filename2 = 
                //with password
        // exec('mysql -u' .$username.' -p '.$password.' '.$dbname. ' < ' . $filename); 
                //without password
        // exec('mysql -u' .$username.' '.$dbname. ' < ' . $filename2);  
        
        // $this->info('Database Imported--' . $filename2);
    }
}
