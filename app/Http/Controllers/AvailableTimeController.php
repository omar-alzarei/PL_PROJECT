<?php

namespace App\Http\Controllers;

use App\Models\AvailableTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailableTimeController extends Controller
{
    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return AvailableTime::with("user")->get();
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


    public function store(Request $request)
    {
        //
        $user = Auth::user();
        $data = $request->validate([
//            "from" => "required|date_format:Y-m-d H:i:s"
            "from" => "required",
            "to" => "required"
        ]);
        $availableTime = new AvailableTime();
        $availableTime->from = $data['from'];
        $availableTime->to = $data['to'];

        $availableTime->user()->associate($user);
        $availableTime->save();

        return $availableTime;

    }

    public function show(int $id)
    {
        //
        $availableTime = AvailableTime::with("user")->find($id) ;
        return $availableTime ?? [ "message" => "available time does not exist"];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AvailableTime  $availableTime
     * @return \Illuminate\Http\Response
     */
    public function edit(AvailableTime $availableTime)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AvailableTime  $availableTime
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AvailableTime $availableTime)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AvailableTime  $availableTime
     * @return \Illuminate\Http\Response
     */
    public function destroy(AvailableTime $availableTime)
    {
        //
    }
}
