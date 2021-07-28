<?php
namespace App\Server;

use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Server\UserChat;

class WebSocket {

    var $master;
    var $sockets = array();
    var $users = array();
    var $debug = false;

    function __construct($address, $port) {
        set_time_limit(0);
        ob_implicit_flush();
        $this->master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("socket_create() failed");
        socket_set_option($this->master, SOL_SOCKET, SO_REUSEADDR, 1) or die("socket_option() failed");
        socket_bind($this->master, $address, $port) or die("socket_bind() failed");
        socket_listen($this->master, 20) or die("socket_listen() failed");
        $this->sockets[] = $this->master;
        $this->userAvatar = 7;
        $this->say("Server Started : " . date('Y-m-d H:i:s'));
        $this->say("Listening on   : " . $address . " port " . $port);
        $this->say("Master socket  : " . $this->master . "\n");

        while (true) {
            $changed = $this->sockets;
            $write = NULL;
            $except = NULL;
            socket_select($changed, $write, $except, NULL);
            foreach ($changed as $socket) {
                if ($socket == $this->master) {
                    $client = socket_accept($this->master);
                    if ($client < 0) {
                        $this->log("socket_accept() failed");
                        continue;
                    } else {
                        $this->connect($client);
                    }
                } else {
                    $bytes = @socket_recv($socket, $buffer, 2048, 0);
                    if ($bytes == 0) {
                        $this->disconnect($socket);
                    } else {
                        $user = $this->getuserbysocket($socket);
                        if (!$user->handshake) {
                            $this->dohandshake($user, $buffer);
                            if(!$user->handshake){
                                try{
                                    if(decrypt(trim($buffer)) == "STOP_SERVER_PLEASE_STOP_SERVER_HAI"){
                                        return false;
                                    }
                                } catch (DecryptException $e) {
                                    $this->disconnect($socket);
                                }
                            }
                        } else {
                            $this->process($user, $this->unwrap($buffer));
                        }
                    }
                }
            }
        }
    }

    function process($user, $msg) {
        /* Extend and modify this method to suit your needs */
        /* Basic usage is to echo incoming messages back to client */
        $this->send($user->socket, $msg);
    }

    function send($client, $msg) {
		if(get_resource_type($client)!="Socket") return;
        $this->say("> " . $msg);
        $msg = $this->wrap($msg);
        if(socket_write($client, $msg, strlen($msg)) === false){
			$this->disconnect($client);
		}
        $this->say("! " . strlen($msg));
    }

    function connect($socket) {
        $user = new UserChat();
        $user->id = uniqid();
        $user->socket = $socket;
        array_push($this->users, $user);
        array_push($this->sockets, $socket);
        socket_set_option($user->socket,
						  SOL_SOCKET,  // socket level
						  SO_SNDTIMEO, // timeout option
						  array(
							"sec"=>5, // Timeout in seconds
							"usec"=>0  // I assume timeout in microseconds
							)
						  );
		//var_dump(socket_get_option($socket, SOL_SOCKET, SO_REUSEADDR));
        $this->log($socket . " CONNECTED!");
        $this->log(date("d/n/Y ") . "at " . date("H:i:s T"));
    }

    function disconnect($socket) {
		if($socket == null) return;
        $found = null;
        $userName = null;
        $userLvl = null;
        $userRoomId = null;
        $handshaked = false;
		$duplicate = false;
        $n = count($this->users);
        for ($i = 0; $i < $n; $i++) {
            if ($this->users[$i]->socket == $socket) {
                $found = $i;
                $userName = $this->users[$i]->userName;
                $userLvl = $this->users[$i]->userLevel;
                $userRoomId = $this->users[$i]->userRoomId;
                $userJoinedRoom = $this->users[$i]->userHasJoinedARoom;
                $handshaked = $this->users[$i]->handshake;
                $duplicate = isset($this->users[$i]->duplicate)?$this->users[$i]->duplicate:false;
                break;
            }
        }
        if (!is_null($found)) {
            array_splice($this->users, $found, 1);
        }
        $index = array_search($socket, $this->sockets);
        socket_close($socket);
        $this->log($socket . " DISCONNECTED!");
        if ($index >= 0) {
            array_splice($this->sockets, $index, 1);
        }
        /*$query = "DELETE FROM room where RoomName='{$userName}' AND IsPremium='1'";
        $con = $this->connection->simpleConnect();
		$res = mysql_query($query,$con);
		$isPremium = mysql_affected_rows($con);
		mysql_close($con);
		if($isPremium>0){
			$msg = array("method"=>"reloadLocation");
			$msg = json_encode($msg);
			$user = null;
			$this->process($user,$msg);
		}*/
        if ($handshaked && !$duplicate) {
            $msg = array("method"=>"someoneDisconnected","user"=>"$userName","roomId"=>$userRoomId,"premiumRoom"=>0);
            $msg = json_encode($msg);
            $user = null;
            $this->process($user,$msg);
        }
    }

    function dohandshake($client, $headers) {
        $this->log("Getting client WebSocket version...");
        if (preg_match("/Sec-WebSocket-Version: (.*)\r\n/", $headers, $match))
            $version = $match[1];
        else
            return false;
        $this->log("Client WebSocket version is {$version}, (required: 13)");
        if ($version == 13) {
            // Extract header variables
            $this->log("Getting headers...");
            if (preg_match("/GET (.*) HTTP/", $headers, $match))
                $root = $match[1];
            if (preg_match("/Host: (.*)\r\n/", $headers, $match))
                $host = $match[1];
            if (preg_match("/Origin: (.*)\r\n/", $headers, $match))
                $origin = $match[1];
            if (preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $headers, $match))
                $key = $match[1];

            $this->log("Client headers are:");
            $this->log("\t- Root: " . $root);
            $this->log("\t- Host: " . $host);
            $this->log("\t- Origin: " . $origin);
            $this->log("\t- Sec-WebSocket-Key: " . $key);

            $this->log("Generating Sec-WebSocket-Accept key...");
            $acceptKey = $key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';
            $acceptKey = base64_encode(sha1($acceptKey, true));

            $upgrade = "HTTP/1.1 101 Switching Protocols\r\n" .
                    "Upgrade: websocket\r\n" .
                    "Connection: Upgrade\r\n" .
                    "Sec-WebSocket-Accept: $acceptKey" .
                    "\r\n\r\n";

            $this->log(
                    "Sending this response to the client #{$client->id}:"
                    . "\r\n" . $upgrade
            );
            socket_write($client->socket, $upgrade);

            $client->handshake = true;
            $this->log("Handshake is successfully done!");

            return true;
        }
        else {
            $this->log(
                    "WebSocket version 13 required"
                    . "(the client supports version {$version})"
            );
            return false;
        }
    }

    function getheaders($req) {
        $r = $h = $o = $sk1 = $data = null;
        if (preg_match("/GET (.*) HTTP/", $req, $match)) {
            $r = $match[1];
        }
        if (preg_match("/Host: (.*)\r\n/", $req, $match)) {
            $h = $match[1];
        }
        if (preg_match("/Origin: (.*)\r\n/", $req, $match)) {
            $o = $match[1];
        }
        if (preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $req, $match)) {
            $this->log("Sec Key1: " . $sk1 = $match[1]);
        }
        if ($match = substr($req, -8)) {
            $this->log("Last 8 bytes: " . $l8b = $match);
        }
        return array($r, $h, $o, $sk1, $l8b);
    }

    function getuserbysocket($socket) {
        $found = null;
        foreach ($this->users as $user) {
            if ($user->socket == $socket) {
                $found = $user;
                break;
            }
        }
        return $found;
    }

    function say($msg = "") {
        echo $msg . "\n";
    }

    function log($msg = "") {
        if ($this->debug) {
            echo $msg . "\n";
        }
    }

    function wrap($msg = "") {
        $frame = Array();
        $encoded = "";
        $frame[0] = 0x81;
        $data = $msg;
        $data_length = strlen($data);

        if ($data_length <= 125) {
            $frame[1] = $data_length;
        } else {
            $frame[1] = 126;
            $frame[2] = $data_length >> 8;
            $frame[3] = $data_length & 0xFF;
        }

        for ($i = 0; $i < sizeof($frame); $i++) {
            $encoded .= chr($frame[$i]);
        }

        $encoded .= $data;
        return $encoded;
    }

    function unwrap($msg = "") {
        $len = $masks = $data = $decoded = null;
        $decoded = "";
        $buffer = $msg;
        $len = ord($buffer[1]) & 127;

        if ($len === 126) {
            $masks = substr($buffer, 4, 4);
            $data = substr($buffer, 8);
        } else if ($len === 127) {
            $masks = substr($buffer, 10, 4);
            $data = substr($buffer, 14);
        } else {
            $masks = substr($buffer, 2, 4);
            $data = substr($buffer, 6);
        }

        for ($index = 0; $index < strlen($data); $index++) {
            $decoded .= $data[$index] ^ $masks[$index % 4];
        }

        return $decoded;
    }

}
?>
