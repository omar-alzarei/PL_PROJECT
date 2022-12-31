<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
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
		$user = Auth::user();
        $all = Favorite::with(['user', 'expert'])->where("user_id", "=", $user->id )->get();
		$result = [] ;
		foreach($all as $fav) {
			$fav->expert->isFavorite = true;
			array_push($result, $fav->expert);
		}
		return response($result, 200);
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
            'expert_id' => "required"
        ]);

        $expert = User::find($data["expert_id"]);
        $favorites = Favorite::where('expert_id', '=', $expert->id)->where("user_id", '=', $user->id)->first();
        if( $favorites ) {
            $favorites->delete();
            return ["message" => 'expert is already exist in your favorite list'];
        }

        $favorite = new Favorite();
        $favorite->user()->associate($user);
        $favorite->expert()->associate($expert) ;
        $favorite->save();
        return $favorite->with(['user', 'expert'])->get();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Favorite  $favorite
     * @return \Illuminate\Http\Response
     */
    public function show(Favorite $favorite)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Favorite  $favorite
     * @return \Illuminate\Http\Response
     */
    public function edit(Favorite $favorite)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Favorite  $favorite
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Favorite $favorite)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Favorite  $favorite
     * @return \Illuminate\Http\Response
     */
    public function destroy(Favorite $favorite)
    {
        //
    }
}
