<?php
namespace App\Server;

class PrivateUsers{
	var $socket;
	var $userX;
	var $userY;

	function __construct($sock,$posX,$posY){
		$this->socket = $sock;
		$this->userX = $posX;
		$this->userY = $posY;
	}
}

?>
