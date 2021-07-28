<?php

namespace App\Http\Controllers;

use App\Room;
use App\RoomAccess;
use App\RoomTenant;
use App\RoomType;
use App\RoomYoutubeAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
  /**
   * Returns all rooms.
   *
   * @return mixed
   */
  public function getAll()
  {
    return Room::with('tenants')
      ->get();
  }

  /**
   * REturns the room created by the user.
   *
   * @param $id
   *   The user ID
   * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
   */
  public function getRoomsCreatedByUser($id)
  {
    return Room::with('tenants')
      ->where('user_id', $id)->get();
  }

  /**
   * Returns the room by room id.
   *
   * @param $id
   * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
   */
  public function getUsersByRoomId($id)
  {
    return $room = Room::with('tenants')
      ->find($id);
  }

  /**
   * Add tenant to a room.
   *
   * @param $id
   * @return string
   */
  public function addTenant($id)
  {
    $params = request()->all();
    $room = Room::find($id);

    $room->tenants()->attach($params['alias_id'], [
      'ip_address' => request()->ip(),
      'role_id' => $params['role_id']
    ]);

    return $room;
  }

  /**
   * Remove tenant in a room.
   *
   * @param $id
   * @param $aliasId
   * @return string
   */
  public function deleteTenant($id, $aliasId)
  {
    $room = Room::find($id);
    $room->tenants()->wherePivot('alias_id', $aliasId)->detach();

    // We delete the room if it's temporary and there is no participant left in the room.
    $tenants = $room->tenants()->count();

    if ($room->room_type_id === 3 && !$tenants) {
      $room->delete();
    }

    return 'ok';
  }

  /**
   * Remove tenant in a room.
   *
   * @param $id
   * @return string
   */
  public function deleteTenantInRooms($aliasId)
  {
    $user = auth()->user();

    $alias = $user->aliases()->where('id', $aliasId)->get()->first();
    $alias->rooms()->detach();
    //$room->tenants()->wherePivot('user_id', $userId)->detach();

    return 'ok';
  }

  /**
   * Display a listing of the resource.
   *
   * @return mixed
   */
  public function gotoRoom($id)
  {
    $room = Room::with('tenants')
      ->find($id);

    return view('room.show', [
      'user' => auth()->user(),
      'room' => $room,
    ]);
  }

  /**
   * Get room types.
   *
   * @return RoomType[]|\Illuminate\Database\Eloquent\Collection
   */
  public function getRoomTypes()
  {
    return RoomType::all();
  }

  /**
   * Get room accesses.
   *
   * @return RoomType[]|\Illuminate\Database\Eloquent\Collection
   */
  public function getRoomAccesses()
  {
    return RoomAccess::all();
  }

  /**
   * Get room youtube accesses.
   *
   * @return RoomType[]|\Illuminate\Database\Eloquent\Collection
   */
  public function getRoomYoutubeAccesses()
  {
    return RoomYoutubeAccess::all();
  }

  /**
   * @return mixed
   */
  public function save(Request $request)
  {
    $params = request()->all();

    $user = Auth::guard('api')->user();

    $validatedData = $request->validate([
      'title' => 'bail|required|unique:rooms,title|min:3',
      'welcome' => 'bail|required|min:3',
      'description' => 'bail|required|min:3',
      'limit' => 'bail|required',
      'language' => 'bail|required',
      'room_youtube_access_id' => 'bail|required',
      'room_access_id' => 'bail|required',
      'room_type_id' => 'bail|required',
    ]);

    $room = Room::create([
      'room_youtube_access_id' => $params['room_youtube_access_id'],
      'room_access_id' => $params['room_access_id'],
      'room_type_id' => $params['room_type_id'],
      'title' => $params['title'],
      'description' => $params['description'],
      'welcome' => $params['welcome'],
      'limit' => $params['limit'],
      'language' => $params['language'],
      'image_path' => '',
      'user_id' => $user->id,
    ]);

    return $this->getUsersByRoomId($room->id);
  }
}
