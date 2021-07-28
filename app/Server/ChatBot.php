<?php
namespace App\Server;

use App\Server\WebSocket;
use App\Server\UserChat;
use App\OnlineUsers;
use App\LogsLogins;
use App\LocMods;
use App\Room;
use App\User;

use Illuminate\Support\Facades\Hash;

class ChatBot extends WebSocket
{

  var $privateChats = array();
  var $youtubePlays = array();

  function process($user, $msg)
  {
    if ($msg == "") return;

    $msg = substr($msg, strpos($msg, "{"), strrpos($msg, "}") - strpos($msg, "{") + 1);
    $this->say("< " . $msg);
    if ($original = json_decode($msg)) {
      switch ($original->method) {
        case "register":
          socket_getpeername($user->socket, $ip, $port);
          $user_data = json_decode(decrypt($original->userId));

          if (strtolower($user_data->username) != strtolower($original->userName)) {
            $status = array("method" => "checksumfail");
            $status = json_encode($status);
            $this->send($user->socket, $status);
            $this->disconnect($user->socket);
            return;
          }

          $user->userId = $user_data->token;
          $user->userName = $user_data->username;
          $logsLogins = LogsLogins::whereRaw('LOWER(username) = ?', [strtolower($user->userName)])->where('is_member', ($user_data->type == "member" ? "1" : "0"))->orderBy('id', 'DESC')->first();
          $non_hashed = " ";
          if ($user_data->type == "member") {
            $validUser = User::whereRaw('LOWER(username) = ?', [trim(strtolower($user->userName))])->first();
            if ($validUser) {
              $non_hashed = trim($user_data->username) . "::" . $ip . "::" . $validUser->password;
            }
          } else {
            $non_hashed = trim($user_data->username) . "::" . $user_data->ip . "::" . $user_data->time;
          }
          if ($logsLogins && Hash::check($non_hashed, $logsLogins->token)) {
          } else {
            $status = array("method" => "checksumfail");
            $status = json_encode($status);
            $this->send($user->socket, $status);
            $this->disconnect($user->socket);
            return;
          }

          $user->userType = $user_data->type;
          $user->userStatus = "normal";
          $user->userX = $original->userX;
          $user->userY = $original->userY;
          $user->userHasJoinedARoom = false;
          $user->userRoomId = 0;
          $user->userCatId = 0;
          $user->userInPrivateChat = false;
          $user->userWindowHeight = $original->userWindowHeight;
          $user->userWindowWidth = $original->userWindowWidth;
          $user->userAvatar = $original->userAvatar;
          if ($user->userType == "member") {
            $theUser = User::whereRaw('LOWER(username) = ?', [trim(strtolower($user->userName))]);
            if ($theUser->count() == 0) {
              $user->userLevel = 0;
              $user->theUserID = 0;
            } else {
              $opt = $theUser->first();
              var_dump($opt->member_level);
              $user->userLevel = $opt->member_level;
              $user->theUserID = $opt->id;
              $isPremium = 0;
              $roomLanguage = $opt->language;
              $user->userIsPremium = $isPremium;
              /*if($isPremium){
                $query="INSERT INTO room (RoomName,RoomType,RoomLimit,CatID,isModified,Language,IsPremium,ord) VALUES ('".ucfirst($user->userName)."','1','30','21','0','$roomLanguage','$isPremium','0')";
                $this->connection->query($query);
                if($user->userLevel == 1)	$user->userLevel = 2;
                $status = array("method"=>"reloadLocation");
                $status = json_encode($status);
                $this->process($user,$status);
              }*/
              /*$locMod = LocMods::where('userid',$user->theUserID)->where('catid',$user->userCatId);
              if($locMod->count()>1){
                $out = $locMod->first();
                if($out->type == 0){
                  $user->userLevel = 7;
                } else{
                  $user->userLevel = 8;
                }
              }*/
            }
          } else {
            $user->userLevel = 0;
            $user->userIsPremium = 0;
          }
          if (OnlineUsers::where('username', trim($user->userName))->count() > 0) {
            $status = array("method" => "duplicate");
            $status = json_encode($status);
            $user->duplicate = true;
            $this->send($user->socket, $status);
            $this->disconnect($user->socket);
            return;
          } else {
            $online_user = new OnlineUsers;
            $online_user->username = $user->userName;
            $online_user->room_id = $user->userRoomId;
            $online_user->cat_id = $user->userCatId;
            $online_user->member_type = $user->userType;
            $online_user->ip = $ip;
            $online_user->save();
          }

          $msg = array("method" => "joinRoom", "newRoomId" => $original->userRoomId, "newCatId" => $original->userCatId);
          $msg = json_encode($msg);
          $this->process($user, $msg);
          break;

        case "chat":
          if ($original->message != "") {
            $status = array("method" => "message", "user" => $user->userName, "chatmsg" => substr($original->message, 0, 150));
            if ((($user->userLevel > 2)) && ((substr($original->message, 0, 6) == "/warn ") || substr($original->message, 0, 3) == "/w ")) { // || ($user->userLevel==2 && $user->userName == $this->connection->getRoomName($user->userRoomId)) IN case of he wants premium to warn on his own room
              if ((substr($original->message, 0, 6) == "/warn ")) $original->message = str_replace("/warn ", "", $original->message);
              else if ((substr($original->message, 0, 3) == "/w ")) $original->message = str_replace("/w ", "", $original->message);
              $status = array("method" => "warning", "user" => $user->userName, "warnmsg" => $original->message);
            } else if (((substr($original->message, 0, 11) == "/think ") || substr($original->message, 0, 3) == "/t ")) { // || ($user->userLevel==2 && $user->userName == $this->connection->getRoomName($user->userRoomId)) ''
              if ((substr($original->message, 0, 11) == "/think ")) $original->message = str_replace("/think ", "", $original->message);
              else if ((substr($original->message, 0, 3) == "/t ")) $original->message = str_replace("/t ", "", $original->message);
              $status = array("method" => "think", "user" => $user->userName, "thinkmsg" => $original->message);
            }
            $status = json_encode($status);
            foreach ($this->users as $thisUser) {
              if ($user->socket == $thisUser->socket) continue;
              if ($user->userRoomId != $thisUser->userRoomId) continue;
              $this->send($thisUser->socket, $status);
            }
          }
          if ($user->userStatus == "normal") break;

          case "setAwake":
          $status = array("method" => "userAwake", "user" => $user->userName);
          $status = json_encode($status);
          $user->userStatus = "normal";
          foreach ($this->users as $thisUser) {
            if ($user->socket == $thisUser->socket) continue;
            if ($user->userRoomId != $thisUser->userRoomId) continue;
            $this->send($thisUser->socket, $status);
          }
          break;

          case "setAway":
          if ($user->userStatus == "away") break;
          $user->userStatus = "away";
          $status = array("method" => "userAway", "user" => $user->userName);
          $status = json_encode($status);
          foreach ($this->users as $thisUser) {
            if ($user->socket == $thisUser->socket) continue;
            if ($user->userRoomId != $thisUser->userRoomId) continue;
            $this->send($thisUser->socket, $status);
          }
          break;

          case "resized":
          $user->userWindowWidth = $original->userWindowWidth;
          $user->userWindowHeight = $original->userWindowHeight;
          $status = array("method" => "resized", "user" => $user->userName, "winWidth" => $user->userWindowWidth, "winHeight" => $user->userWindowHeight);
          $status = json_encode($status);
          foreach ($this->users as $thisUser) {
            if ($user->socket == $thisUser->socket) continue;
            if ($user->userRoomId != $thisUser->userRoomId) continue;
            $this->send($thisUser->socket, $status);
          }
          break;

          case "moveXY":
          $user->userX = $original->posX;
          $user->userY = $original->posY;
          $status = array("method" => "moveXY", "user" => $user->userName, "posX" => $user->userX, "posY" => $user->userY);
          $status = json_encode($status);
          foreach ($this->users as $thisUser) {
            if ($user->socket == $thisUser->socket) continue;
            if ($user->userRoomId != $thisUser->userRoomId) continue;
            $this->send($thisUser->socket, $status);
          }
          if ($user->userStatus == "normal") break;

          case "setAwakeForced2":
          $status = array("method" => "userAwake", "user" => $user->userName);
          $user->userStatus = "normal";
          $status = json_encode($status);
          foreach ($this->users as $thisUser) {
            if ($user->socket == $thisUser->socket) continue;
            if ($user->userRoomId != $thisUser->userRoomId) continue;
            $this->send($thisUser->socket, $status);
          }
          break;

          case "kickAndEndPremium":
          if ($user->userLevel == 3 || $user->userLevel == 4)
            $premium = 1;
          else
            $premium = 0;

          case "kickUser":
          if ($user->userLevel < 3) return false;
          foreach ($this->users as $thisUser) {
            if ($thisUser->userName == strtolower($original->user)) {
              if ($thisUser->userLevel == 3) return false;
              if ($user->userLevel == 4 && ($thisUser->userLevel == 4)) return false;
              if ($user->userLevel == 5 && ($thisUser->userLevel == 4 || $thisUser->userLevel == 5 || $thisUser->userLevel == 7)) return false;
              if ($user->userLevel == 6 && ($thisUser->userLevel == 4 || $thisUser->userLevel == 5 || $thisUser->userLevel == 6 || $thisUser->userLevel == 7 || $thisUser->userLevel == 8)) return false;
              if ($user->userLevel == 7 && ($thisUser->userLevel == 4 || $thisUser->userLevel == 5 || $thisUser->userLevel == 7)) return false;
              if ($user->userLevel == 8 && ($thisUser->userLevel == 4 || $thisUser->userLevel == 5 || $thisUser->userLevel == 6 || $thisUser->userLevel == 7 || $thisUser->userLevel == 8)) return false;
              /* Premium cannot kick user from complete chat
              $query = "SELECT * FROM room WHERE LOWER(RoomName)=LOWER('{$user->userName}') AND RoomID='{$user->userRoomId}'";
              if($user->userLevel == 2){
                if(($thisUser->userLevel == 4 || $thisUser->userLevel == 5 || $thisUser->userLevel == 6 || $thisUser->userLevel == 7 || $thisUser->userLevel == 8)) return false;
                $opt = mysql_num_rows($this->connection->query($query));
                if($opt==0) return false;
              }*/
              break;
            }
          }
          $status = array("method" => "kicked");
          /*if(isset($premium) && $premium == 1){
            $status = array("method"=>"kickedPremium");
            $this->connection->query("UPDATE member SET premium='0',PremiumExpireDate=NULL WHERE UserName='{$original->user}'");
          }*/

          $status = json_encode($status);
          foreach ($this->users as $thisUser) {
            if ($thisUser->userName == $original->user) {
              socket_getpeername($thisUser->socket, $ip, $port);
              //$this->connection->query("INSERT INTO ipbans (bannedip, reason, until, banby) VALUES('$ip','N/A',CURRENT_TIMESTAMP,'{$user->userName}')");
              // Currently hold ipban table
              $this->send($thisUser->socket, $status);
              $this->disconnect($thisUser->socket);
              break;
            }
          }
          break;
        case "kickUserFromLocation":
          if ($user->userLevel < 2) return false;
          foreach ($this->users as $thisUser) {
            if ($thisUser->userName == $original->user) {
              if ($thisUser->userLevel == 3) return false;
              if ($user->userLevel == 2 && ($thisUser->userLevel == 4 || $thisUser->userLevel == 5 || $thisUser->userLevel == 6 || $thisUser->userLevel == 7)) return false;
              else {
                if ($user->userLevel == 2) {
                  // Skipping premium user room
                  /*$query = "SELECT * FROM room WHERE RoomName='{$user->userName}'";
                  $res = $this->connection->query($query);
                  $opt = mysql_fetch_array($res);
                  if($opt['RoomID'] != $user->userRoomId) return false;*/
                }
              }
              if ($user->userLevel == 4 && ($thisUser->userLevel == 4)) return false;
              if ($user->userLevel == 5 && ($thisUser->userLevel == 4 || $thisUser->userLevel == 5)) return false;
              if ($user->userLevel == 6 && ($thisUser->userLevel == 4 || $thisUser->userLevel == 5 || $thisUser->userLevel == 6 || $thisUser->userLevel == 7 || $thisUser->userLevel == 8)) return false;
              if ($user->userLevel == 7 && ($thisUser->userLevel == 4 || $thisUser->userLevel == 5 || $thisUser->userLevel == 7)) return false;
              if ($user->userLevel == 8 && ($thisUser->userLevel == 4 || $thisUser->userLevel == 5 || $thisUser->userLevel == 6 || $thisUser->userLevel == 7 || $thisUser->userLevel == 8)) return false;
              break;
            }
          }
          OnlineUsers::where('username', $original->user)->update(["room_id" => "-1000", "cat_id" => "-1000"]);
          $status = array("method" => "kickedFromLocation", "locationId" => $user->userRoomId);
          $status = json_encode($status);
          foreach ($this->users as $thisUser) {
            if ($thisUser->userName == $original->user) {
              $this->send($thisUser->socket, $status);
              break;
            }
          }
          foreach ($this->users as $thisUser) {
            $status = array("method" => "someoneDisconnected", "user" => $original->user);
            $status = json_encode($status);
            $this->send($thisUser->socket, $status);
          }
          break;
        case "avatarchange":
          $newAvatar = $original->avatar;
          $exploder = explode(",", $newAvatar);
          if (count($exploder) != 9) break;
          $user->userAvatar = $newAvatar;
          $status = array("method" => "avatarchange", "user" => $user->userName, "newAvatar" => $newAvatar);
          $status = json_encode($status);
          foreach ($this->users as $thisUser) {
            if ($user->socket == $thisUser->socket) continue;
            if ($user->userRoomId != $thisUser->userRoomId) continue;
            $this->send($thisUser->socket, $status);
          }
          break;
        case "someoneDisconnected":
          if (trim($original->user) == "") return false;
          OnlineUsers::whereRaw('LOWER(username) = ?', [strtolower($original->user)])->delete();
          foreach ($this->users as $thisUser) {
            $status = array("method" => "someoneDisconnected", "user" => $original->user, "roomId" => $original->roomId, "premiumRoom" => $original->premiumRoom);
            $status = json_encode($status);
            $this->send($thisUser->socket, $status);
          }
          break;
        case "youtubeplay":
          if ($user->userIsPremium != 1) break;
          $ytURL = $original->ytURL;
          $status = array("method" => "playVideo", "user" => $user->userName, "videoURL" => $ytURL);
          $status = json_encode($status);
          $ytre = "/^^(https?\:\/\/)?.*(((youtu|y2u).be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))(.*v=)?([^#\&\?]*)/";
          preg_match($ytre, $ytURL, $ytmatch);
          if (!isset($ytmatch[10])) break;
          $url = "https://www.googleapis.com/youtube/v3/videos?id=" . $ytmatch[10] . "&part=contentDetails&key=AIzaSyDJ7CrR0nr-zN0hjEo-WUoi2ej32H54Ofg";
          $content = json_decode(file_get_contents($url));
          if (!$content) break;
          if (!isset($content->items)) break;
          if (!isset($content->items[0]->contentDetails)) break;
          if (!isset($content->items[0]->contentDetails->duration)) break;
          if (empty(trim($content->items[0]->contentDetails->duration))) break;
          $date_interval = new DateInterval($content->items[0]->contentDetails->duration);
          $start = new DateTime("@0");
          $start->add($date_interval);
          $duration = $start->getTimeStamp();

          if (!isset($this->youtubePlays[$user->userRoomId])) $this->youtubePlays[$user->userRoomId] = array();
          $start_time = time();
          if (count($this->youtubePlays[$user->userRoomId]) > 0) $start_time = $this->youtubePlays[$user->userRoomId][count($this->youtubePlays[$user->userRoomId]) - 1]["endtime"];
          $this->youtubePlays[$thisUser->userRoomId][] = array("url" => $ytURL, "time" => $start_time, "endtime" => $start_time + $duration, "duration" => $duration, "username" => $user->userName);
          foreach ($this->users as $thisUser) {
            if ($user->socket == $thisUser->socket) continue;
            if ($user->userRoomId != $thisUser->userRoomId) continue;
            $this->send($thisUser->socket, $status);
          }
          break;
        case "youtubestop":
          if ($user->userLevel < 1) break;
          $ytURL = $original->ytURL;
          $status = array("method" => "stopVideo", "user" => $user->userName, "videoURL" => $ytURL);
          $status = json_encode($status);
          if (isset($this->youtubePlays[$user->userRoomId])) array_shift($this->youtubePlays[$user->userRoomId]);
          foreach ($this->users as $thisUser) {
            if ($user->socket == $thisUser->socket) continue;
            if ($user->userRoomId != $thisUser->userRoomId) continue;
            $this->send($thisUser->socket, $status);
          }
          break;
        case "reloadLocation":
          foreach ($this->users as $thisUser) {
            $status = array("method" => "loadLocation");
            $status = json_encode($status);
            $this->send($thisUser->socket, $status);
          }
          break;
        case "joinRoom":
          if ($user->userRoomId != 0) {
            foreach ($this->users as $thisUser) {
              $status = array("method" => "someoneDisconnected", "user" => $user->userName);
              $status = json_encode($status);
              $this->send($thisUser->socket, $status);
            }
          }
          $roomData = Room::where('id', $original->newRoomId)->first();
          $roomOnline = $roomData->online_users();
          $error = 0;
          if ($roomData->count() > 0) {
            $thisRoomType = $roomData->room_type;
            $thisRoomLimit = $roomData->room_limit;
            $thisRoomOnline = $roomOnline->count();
            /*if(!($user->userIsPremium && $roomData['RoomName']==$user->userName)){
              if($thisRoomType > 1 && $user->userLevel == 0) $error = 1;
              if($thisRoomType == 3 && !$user->userIsPremium) $error = 1;
              if($thisRoomType == 4 && $user->userLevel <3 ) $error = 1;
              if($user->userLevel < 2){
                if($thisRoomOnline >= $thisRoomLimit) $error = 2;
              }
            }*/// Skipping premium part for the moment
          } else {
            $error = 1;
          }
          if ($error != 0) {
            $msg = array("method" => "error", "message" => "No permission to access the room", "type" => $error, "user" => $user->userName, "roomID" => $user->userRoomId, "catID" => $user->userCatId);
            $msg = json_encode($msg);
            $this->send($user->socket, $msg);
            return false;
          }
          OnlineUsers::where('username', $user->userName)->update(["room_id" => "{$original->newRoomId}", "cat_id" => "{$original->newCatId}"]);
          $user->userRoomId = $original->newRoomId;
          $user->userCatId = $original->newCatId;
          $locMod = LocMods::where('userid', $user->theUserID)->where('roomid', $user->userRoomId);
          if ($user->userLevel == 4 || $user->userLevel == 5 || $user->userLevel == 6 || $user->userLevel == 7 || $user->userLevel == 8) $user->userLevel = 1;
          $user_detail = User::whereRaw('LOWER(username) = ?', [trim(strtolower($user->userName))]);
          if ($user_detail->count() > 0) {
            $user_actual_detail = $user_detail->first();
            $isPremium = false;
            if ($isPremium && ($user->userLevel != 3)) $user->userLevel = 2; // TODO : HERE deciding who can override the current admin rule
          }
          if ($locMod->count() > 0 && $user->userLevel != 3) {
            $out = $locMod->first();
            if ($out->type == 0) {
              $user->userLevel = 7;
            } else {
              $user->userLevel = 8;
            }
          }
          /*$query = "SELECT * FROM langadmin WHERE userid='{$user->theUserID}' AND language IN (SELECT Language FROM room WHERE RoomID='{$user->userRoomId}')";
          $rslangmin = $this->connection->query($query);
          if($rslangmin && (mysql_num_rows($rslangmin) > 0)){
            $optlangmin = mysql_fetch_assoc($rslangmin);
            switch($optlangmin['admintype']){
              case 0:
                $user->userLevel = 4;
                break;
              case 1:
                $user->userLevel = 5;
                break;
              case 2:
                if($user->userLevel != 7) $user->userLevel = 6;
                break;
            }
          }*/// Skipping language admin for the moment
          $user->userStatus = "normal";
          $msg = array("method" => "connected", "userName" => $user->userName, "userType" => $user->userType, "userLevel" => $user->userLevel, "posX" => $user->userX, "posY" => $user->userY, "userWidth" => $user->userWindowWidth, "userHeight" => $user->userWindowHeight, "isInPrivateChat" => $user->userInPrivateChat, "isPremium" => $user->userIsPremium, "userAvatar" => $user->userAvatar);
          $msg = json_encode($msg);
          $otherUsers = array();
          foreach ($this->users as $thisUser) {
            if ($user->socket == $thisUser->socket) continue;
            if ($user->userRoomId != $thisUser->userRoomId) continue;
            $otherUsers[] = array("userName" => $thisUser->userName, "posX" => $thisUser->userX, "posY" => $thisUser->userY, "userType" => $thisUser->userType, "userLevel" => $thisUser->userLevel, "status" => $thisUser->userStatus, "userWidth" => $thisUser->userWindowWidth, "userHeight" => $thisUser->userWindowHeight, "isInPrivateChat" => $thisUser->userInPrivateChat, "isPremium" => $thisUser->userIsPremium, "userAvatar" => $thisUser->userAvatar);
            $this->send($thisUser->socket, $msg);
          }
          $msg = array("method" => "getConnected", "otherUser" => $otherUsers, "level" => $user->userLevel, "userRoomId" => $user->userRoomId, "userCatId" => $user->userCatId);
          $msg = json_encode($msg);
          $this->send($user->socket, $msg);
          if (isset($this->youtubePlays[$user->userRoomId]) && count($this->youtubePlays[$user->userRoomId] > 0)) {
            $tmp_array = array();
            foreach ($this->youtubePlays[$user->userRoomId] as $youtube_player) {
              if (time() > $youtube_player["endtime"]) {
                continue;
              }
              $tmp_array[] = $youtube_player;
              $start_time = time() - $youtube_player["time"];
              if ($start_time < 0) $start_time = 0;
              $status = array("method" => "playVideo", "user" => $youtube_player["username"], "videoURL" => $youtube_player["url"], "start" => $start_time);
              $msg = json_encode($status);
              $this->send($user->socket, $msg);
            }
            $this->youtubePlays[$user->userRoomId] = $tmp_array;
          }
          break;
        //Private chat section for deluxe
        case "privateChatReq":
          $msg = array("method" => "privateChatReq", "username" => $user->userName);
          $msg = json_encode($msg);
          foreach ($this->users as $thisUser) {
            if ($user->socket == $thisUser->socket) continue;
            if ($thisUser->userName == $original->username) {
              $this->send($thisUser->socket, $msg);
              break;
            }
          }
          break;
        case "rejectChat":
          $errortype = 3;
          switch ($original->reason) {
            case "norespond":
              $errortype = 3;
              break;
            case "rejected":
              $errortype = 4;
              break;
            case "ignoring":
              $errortype = 5;
              break;
            case "alreadyinchat":
              $errortype = 6;
              break;
          }
          $msg = array("method" => "error", "message" => "Chat rejection", "type" => $errortype, "user" => $user->userName);
          $msg = json_encode($msg);
          foreach ($this->users as $thisUser) {
            if ($user->socket == $thisUser->socket) continue;
            if ($thisUser->userName == $original->username) {
              $this->send($thisUser->socket, $msg);
              break;
            }
          }
          break;
        case "chatAccepted":
          $msg = array("method" => "privateChatReqAccept", "username" => $user->userName);
          $msg = json_encode($msg);
          foreach ($this->users as $thisUser) {
            if ($user->socket == $thisUser->socket) continue;
            if ($thisUser->userName == $original->username) {
              if (!isset($this->privateChats[$original->username])) $this->privateChats[$original->username] = new privateArea($thisUser->socket, rand(0, 400), rand(0, 250));
              $this->privateChats[$original->username]->otherUsers[$user->userName] = new privateUsers($user->socket, rand(0, 400), rand(0, 250));
              $this->send($thisUser->socket, $msg);
              break;
            }
          }
          break;
        case "fetchAllPriv":
          if (!isset($this->privateChats[$original->window])) break;
          $others = array();
          foreach ($this->privateChats[$original->window]->otherUsers as $key => $value) {
            foreach ($this->users as $thisUser) {
              if ($thisUser->userName == $key) {
                $others[] = array($key, $value->userX, $value->userY, $thisUser->userAvatar, $thisUser->userLevel);
                break;
              }
            }
          }
          foreach ($this->users as $thisUser) {
            if ($thisUser->userName == $original->window) {
              $others[] = array($original->window, $this->privateChats[$original->window]->myPosX, $this->privateChats[$original->window]->myPosY, $thisUser->userAvatar, $thisUser->userLevel);
              break;
            }
          }
          $msg = array("method" => "privOtherUsers", "window" => $original->window, "others" => $others);
          $msg = json_encode($msg);
          foreach ($this->privateChats[$original->window]->otherUsers as $key => $value) {
            $this->send($value->socket, $msg);
          }
          $this->send($user->socket, $msg);
          break;
        case "privateChatMessage":
          if (!isset($this->privateChats[$original->window])) break;
          $msg = array("method" => "privateChatMessage", "window" => $original->window, "username" => $user->userName, "message" => $original->message);
          $msg = json_encode($msg);
          if ($original->window != $user->userName) $this->send($this->privateChats[$original->window]->socket, $msg);
          foreach ($this->privateChats[$original->window]->otherUsers as $thisUser) {
            if ($user->socket == $thisUser->socket) continue;
            $this->send($thisUser->socket, $msg);
          }
          break;
        case "windowClose":
          $original->window = $user->userName;
          if (!isset($this->privateChats[$user->userName])) break;
          $msg = array("method" => "windowClose", "window" => $original->window);
          $msg = json_encode($msg);
          if ($original->window != $user->userName) $this->send($this->privateChats[$original->window]->socket, $msg);
          foreach ($this->privateChats[$original->window]->otherUsers as $thisUser) {
            if ($user->socket == $thisUser->socket) continue;
            $this->send($thisUser->socket, $msg);
          }
          unset($this->privateChats[$original->window]);
          break;
        case "privateChatClose":
          if (!isset($this->privateChats[$original->window])) break;
          $msg = array("method" => "privateChatClose", "window" => $original->window, "user" => $user->userName);
          $msg = json_encode($msg);
          if ($original->window != $user->userName) $this->send($this->privateChats[$original->window]->socket, $msg);
          foreach ($this->privateChats[$original->window]->otherUsers as $thisUser) {
            if ($user->socket == $thisUser->socket) continue;
            $this->send($thisUser->socket, $msg);
          }
          unset($this->privateChats[$original->window]->otherUsers[$user->userName]);
          break;
        case "privateMoveXY":
          if (!isset($this->privateChats[$original->window])) break;
          $msg = array("method" => "privateMoveXY", "window" => $original->window, "username" => $user->userName, "userX" => $original->posX, "userY" => $original->posY);
          $msg = json_encode($msg);
          if ($original->window == $user->userName) {
            $this->privateChats[$original->window]->myPosX = $original->posX;
            $this->privateChats[$original->window]->myPosY = $original->posY;
          }
          var_dump(($original->window != $user->userName));
          if ($original->window != $user->userName) $this->send($this->privateChats[$original->window]->socket, $msg);
          foreach ($this->privateChats[$original->window]->otherUsers as $key => $thisUser) {
            if ($original->window == $key) {
              $thisUser->userX = $original->posX;
              $thisUser->userY = $original->posY;
            }
            if ($user->socket == $thisUser->socket) continue;
            $this->send($thisUser->socket, $msg);
          }
          break;
        case "setPrivateChat":
          $user->userInPrivateChat = true;
          $msg = array("method" => "setUserPrivate", "user" => $user->userName);
          $msg = json_encode($msg);
          foreach ($this->users as $thisUser) {
            if ($user->socket == $thisUser->socket) continue;
            if ($user->userRoomId != $thisUser->userRoomId) continue;
            $this->send($thisUser->socket, $msg);
          }
          break;
        case "removePrivateChat":
          if (isset($this->privateChats[$user->userName])) unset($this->privateChats[$user->userName]);
          $user->userInPrivateChat = false;
          $msg = array("method" => "setUserNonPrivate", "user" => $user->userName);
          $msg = json_encode($msg);
          foreach ($this->users as $thisUser) {
            if ($user->socket == $thisUser->socket) continue;
            if ($user->userRoomId != $thisUser->userRoomId) continue;
            $this->send($thisUser->socket, $msg);
          }
          break;
        default :
          //if(isset($this->privateChats[$user->userName]))	unset($this->privateChats[$user->userName]);
          $this->process($user, json_encode(array("method" => "windowClose")));
          $this->disconnect($user->socket);
          $this->send($user->socket, $msg . " disconnection send");
          break;
      }
    } else {
      $this->process($user, json_encode(array("method" => "windowClose")));
      $this->disconnect($user->socket);
      $this->send($user->socket, $msg . " disconnection send");
    }
  }
}

?>
