<?php
namespace App\Server;

class PrivateArea{
	var $myPosX;
	var $myPosY;
	var $socket;
	var $otherUsers = array();
	function __construct($sock,$posX,$posY){
		$this->socket = $sock;
		$this->myPosX = $posX;
		$this->myPosY = $posY;
	}
}

?>
