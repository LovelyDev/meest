<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class StopServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:stop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Server Stopper';

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
        print "Verifying if server is running...\n";
        $host = "0.0.0.0";
		$port = env('WSSERVER_PORT');
		$waitTimeoutInSeconds = 1;
		if($fp = @fsockopen($host,$port,$errCode,$errStr,$waitTimeoutInSeconds)){ // Checking chat server
            // We already have server running, no need to run another instance again.
            print "Server already running, we stop the server\n";
            fwrite($fp,encrypt("STOP_SERVER_PLEASE_STOP_SERVER_HAI"));
            fclose($fp);
            print "Request to stop server is sent.\n";
            return false;
        } else {
            print "No instance of server found, nothing to do...\n";
        }
    }
}
