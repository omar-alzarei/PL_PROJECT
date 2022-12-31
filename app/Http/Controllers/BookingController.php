<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
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
        //
        $data = $request->validate([
            "expert_id" => "required",
            "from" => "required" ,
            "to" => "required"
        ]);

        if($data['expert_id'] == $user->id) {
            return ['message' => "user and expert is the same person"];
        }

        $expert = User::find($data['expert_id']);
        $availableTimes =  $expert->availableTimes()->get();

        $from_date = Carbon::parse($data['from']);
        $to_date = Carbon::parse($data['to']);
		$now = Carbon::now();
		
		$dt_from = $from_date->timezone('Africa/Cairo');
	
		if(Carbon::now()->gt($dt_from)) {
			return [ "message" => "date must be greater than to current date"];
		}
        if( $from_date->gt($to_date)) {
            return [ "message" => "from date must be less than to data"];
        }

        $expertHasAvailableTime = false;

        foreach($availableTimes as $availableTime) {
            $current_from_date = Carbon::parse($availableTime->from);
            $current_to_date = Carbon::parse($availableTime->to);
            if( $from_date->gte($current_from_date) && $to_date->lte($current_to_date)) {
                $expertHasAvailableTime = true;
                break;
            }
        }	
        $all_reservations = Booking::where("from", '>', now())->get();
        $hasPreviousReservation = false;
        foreach ($all_reservations as $reservation) {
            $current_from_date = Carbon::parse($reservation->from);
            $current_to_date = Carbon::parse($reservation->to);
            if($from_date->lte($current_to_date) && $current_from_date->lte($to_date)) {
                $hasPreviousReservation = true;
                break;
            }
        }


        if(!$expertHasAvailableTime || $hasPreviousReservation) {
            return [ 'message' => "this time is not available"] ;
        }

        if( $user->wallet < $expert->consult_price) {
            return ["message" => "your must have greater than" . $expert->consult_price . 'to continue'];
        }

        $booking = new Booking();
        $booking->from = $data['from'];
        $booking->to = $data['to'];
        $user->wallet -= $expert->consult_price;
        $expert->wallet += $expert->consult_price;
        $booking->user()->associate($user);
        $booking->expert()->associate($expert);
        $booking->save();
        $user->save();
        $expert->save();

        return $booking->with(["user", 'expert'])->get();

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function show(Booking $booking)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function edit(Booking $booking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Booking $booking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function destroy(Booking $booking)
    {
        //
    }
}
