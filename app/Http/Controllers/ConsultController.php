<?php

namespace App\Http\Controllers;

use App\Models\Consult;
use App\Models\Favorite;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsultController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function __construct() {
         $this->middleware('auth:sanctum');
     }

    public function index()
    {
        //
        return Consult::all();
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

        $data = $request->validate([
            'name' =>  'required|string',
            "description" => "required|string",
            "image" => "image|mimes:jpeg,png,jpg,gif,svg|max:2048'"
        ]);

        if($request->file('image')){
            $file= $request->file('image');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file-> move(public_path('/images'), $filename);
            $data['image']= $filename;
        }

        $consult = new Consult();
        $consult->name = $data['name'] ;
        $consult->description = $data['description'] ;
        $consult->image = $data['image'] ;
        $consult->save();
        return response($consult, 201);

    }

    public function show(int $id)
    {
        //
		$user = Auth::user();
        $user_favorites = Favorite::where('user_id', "=", $user->id)->get();
        $consults = Consult::with("users")->find($id);
        forEach($consults->users as $expert) {
			$expert->isFavorite = false;
            foreach($user_favorites as $user_favorite) {
                if($user_favorite->expert_id == $expert->id) {
                    $expert->isFavorite = true;
                    break;
                }else {
                    $expert->isFavorite = false;
                }
            }
        }

      
        return $consults ?? ['message' => "consult not found"];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Consult  $consult
     * @return \Illuminate\Http\Response
     */
    public function edit(Consult $consult)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Consult  $consult
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Consult $consult)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Consult  $consult
     * @return \Illuminate\Http\Response
     */
    public function destroy(Consult $consult)
    {
        //
    }

    public function getConsultByName(Request $request) {
        $data = $request->validate([
            'consulting' => 'required|string'
        ]);
        return Consult::with("users")->where("consulting", 'like', '%'.$data['consulting'].'%')->get();
    }
}
