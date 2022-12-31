<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
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
        $user = Auth::user();
        $data = $request->validate([
            "expert_id" => 'required',
            'evaluation_value' => 'required|integer|between:1,5'
        ]);
		
		$evaluation = Evaluation::where("user_id","=",$user->id)->where('expert_Id', '=', $data['expert_id'])->first();

		if($evaluation) {
			$evaluation->evaluation_value = $data['evaluation_value'];
			$evaluation->save();
			return $evaluation->with(['expert'])->get();
		}else {
			$expert = User::find($data['expert_id']);
			$evaluation = new Evaluation();
			$evaluation->evaluation_value = $data['evaluation_value'];
			$evaluation->expert()->associate($expert);
			$evaluation->user()->associate($user);
			$evaluation->save();
			return $evaluation->with(['expert'])->get();
		}

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Evaluation  $evaluation
     * @return \Illuminate\Http\Response
     */
    public function show(Evaluation $evaluation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Evaluation  $evaluation
     * @return \Illuminate\Http\Response
     */
    public function edit(Evaluation $evaluation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Evaluation  $evaluation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Evaluation $evaluation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Evaluation  $evaluation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Evaluation $evaluation)
    {
        //
    }
}
