<?php

namespace App\Http\Controllers;

use App\AvatarDefault;
use Illuminate\Http\Request;

class AvatarDefaultController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDefaults()
    {
        return AvatarDefault::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AvatarDefault  $avatarDefault
     * @return \Illuminate\Http\Response
     */
    public function show(AvatarDefault $avatarDefault)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AvatarDefault  $avatarDefault
     * @return \Illuminate\Http\Response
     */
    public function edit(AvatarDefault $avatarDefault)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AvatarDefault  $avatarDefault
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AvatarDefault $avatarDefault)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AvatarDefault  $avatarDefault
     * @return \Illuminate\Http\Response
     */
    public function destroy(AvatarDefault $avatarDefault)
    {
        //
    }
}
