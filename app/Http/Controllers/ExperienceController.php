<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExperienceController extends Controller
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
        return Experience::with("user")->get();
    }


    public function create(Request $request)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Experience
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        // get current logged user all information
        $user = Auth::user();

        if($user->is_expert == 0 ) {
            return ['message' => "this user is not expert user"];
        }
        $data = $request->validate([
            'exp_description' => "required|string"
        ]);

        $experience = new Experience();
        $experience->exp_description = $data['exp_description'];

        $experience->user()->associate($user);
        $experience->save();
        return $experience->with("user")->get();
    }

    public function show(int $id)
    {
//         return Experience::findOrFail($id);
        $experice = Experience::find($id) ;
            return $experice ?? ["message" => "experience not found"] ;

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Experience  $experience
     * @return \Illuminate\Http\Response
     */
    public function edit(Experience $experience)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Experience  $experience
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Experience $experience)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Experience  $experience
     * @return \Illuminate\Http\Response
     */
    public function destroy(Experience $experience)
    {
        //
    }
}
