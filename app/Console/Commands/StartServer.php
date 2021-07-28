<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Server\ChatBot;
use App\OnlineUsers;
use App\Room;

class StartServer extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'server:start';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Server Starter';

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
    //print "Verifying if server is running...\n";
//    $host = "0.0.0.0";
//    $port = env('WSSERVER_PORT');
//    $waitTimeoutInSeconds = 1;

print exec("forever resources/js/server/server.js");
    print "passed";
//    if ($fp = @fsockopen($host, $port, $errCode, $errStr, $waitTimeoutInSeconds)) {
//      // Checking chat server
//      // We already have server running, no need to run another instance again.
//      print "Server looks already running\n";
//      fclose($fp);
//      return false;
//    } else {
//      print "No instance of server found, starting new server instance...\n";
//    }
//
//    // @Todo: clear online users.
//    print "Clearing online users\n";
//    //OnlineUsers::truncate();
//
//    // @Todo: clear temporary rooms
//    print "Clearing premium rooms\n";
//    //Room::where('is_premium',1)->delete();
//    print "\n";
//
//    print "Server starting...\n";
//    $master = new ChatBot("0.0.0.0", $port);
//    print "Server stopped by request...\n";
  }
}
