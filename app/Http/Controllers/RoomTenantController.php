<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoomTenantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function gotoRoom($id)
    {
        $room = Room::find($id);

        return view('room.show', [
            'user' => auth()->user(),
            'room' => $room,
        ]);
    }
}
