<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Experience;
use App\Models\Favorite;
use App\Models\User;
use App\Models\Message;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use \stdClass;

class UserController extends Controller
{
	public function __construct(){
		$this->middleware(['api','cors']);
	}


    public function store(Request $requset){
    }

 /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request) {
		$is_expert = $request["is_expert"] === "true" ? 1 : 0 ;
			
        $data = $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|string|max:255|email|unique:users',
            'password'=>'required|string|min:8',
            'is_expert'=>'required',
            'consult_price' => 'required_if:is_expert,1',
            'phone_number' => 'required_if:is_expert,1',
            'address' => 'required_if:is_expert,1',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'is_expert'=> $is_expert,
            'consult_price'=> $is_expert ? $data['consult_price'] : null ,
            'phone_number'=> $is_expert ? $data['phone_number'] : null,
            'address'=> $is_expert ? $data['address']  : null,
        ]);
		
		$user = User::find($user->id);
        $token = $user->createToken('apiToken')->plainTextToken;
        $res = [
            'user' => $user,
            'token' => $token,
             'Message' => 'Account has been successfully registered'
        ];
        return response($res, 201);
    }


    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $data['email'])->first();

        // if user does not exist
        // or user exist but the password is incorrect
        // return error message

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response([
                'Message' => 'incorrect username or password'
            ], 401);
        }

        $token = $user->createToken('apiToken')->plainTextToken;
        $res = [
            'user' => $user,
            'token' => $token,
            'Message' => 'Logged in successfully'
        ];

        return response($res, 201);
    }

    public function logout(){
        auth()->user()->tokens()->delete();
        return [
            'message' => 'user logged out'
        ];
    }

    public function index() {
        return User::all();
    }

    public function show(int $id) {
        $user = User::with(["experiences", 'consults', "availableTimes",'favorites', 'get_evaluations'])->find($id);
		$auth_user = Auth::user();
        if(!$user)
            return ['message' => "user does not exist"];
        if($user->is_expert == 0 )
            return $user;

        $evaluations = $auth_user->get_evaluations()->where("expert_Id","=",$id)->first();
        $user->average = $evaluations ? $evaluations->evaluation_value : 0 ;
        return $user;
    }

    public function getBooking() {
        $user = Auth::user();
        return $user->bookings->where("from", ">", now());


    }


    public function getAppointments() {
        $user = Auth::user();
        if(!$user)
            return [ "message" => "unvalid user"];
          else
        $all = Booking::with("user")->where("expert_id", "=", $user->id)->where("from", ">", now())->orderBy("from","asc")->get();
		$response = [];
		foreach($all as $element) {
			$object = new stdClass();
			$object->from = $element->from;
			$object->to = $element->to;
			$object->user = $element->user;
			array_push($response, $object);
		}

		return $response;

    }
    // User::with(["experiences", 'consults', "availableTimes",'favorites'])

    public function getMessages($id) {
        $user = Auth::user();
        if(!$user)
            return [ "message" => "unvalid user"];

        return Message::where([["sender_id", "=", $user->id],["receiver_id", "=", $id]])->orWhere([["sender_id", "=", $id],["receiver_id", "=", $user->id]])->get();
    }

    public function getExperts() {
        $user = Auth::user();
        $user_favorites = Favorite::where('user_id', "=", $user->id)->get();
        $experts =  User::with("consults")->where("is_expert", "=", 1)->get();
        forEach($experts as $expert) {
            foreach($user_favorites as $user_favorite) {
                if($user_favorite->expert_id == $expert->id) {
                    $expert->isFavorite = true;
                    break;
                }else {
                    $expert->isFavorite = false;
                }
            }
			$evaluations = $expert->evaluations()->get();
			$sum = 0 ;
			$count = 0 ;
			foreach($evaluations as $evaluation) {
				$count += 1;
				$sum += $evaluation->evaluation_value;
			}
			$average = $count == 0 ? 0 : (int)($sum/$count);
			/*
			if($average < 5 ) {
				$expert->average = 0;
			} else if($average > 5 && $average < 10) {
				$expert->average = 1;
			}else if($average > 10 && $average < 15) {
				$expert->average = 2;
			}else if($average > 15 && $average < 20) {
				$expert->average = 3;
			}else if($average > 20 && $average < 25) {
				$expert->average = 4;
			}else 
				$expert->average = 5;
			*/
			$expert->average = $average ;
        }

        return $experts;

    }

    public function getExpertByName(string $name) {
//        return User::where([['is_expert', '=', 1],
//            ['name', '=', $name]])->get();

        return User::with("consults")->where('is_expert', '=', 1)->where('name', 'like', '%'.$name.'%')->get();
    }

    public function getAvailableTimeOfAnExpert(int $id) {
        return User::find($id)->availableTimes()->get();
    }

    public function editExpertProfile(Request $request) {
        $user = Auth::user();
		$expert = User::find($user->id);
        $data = $request->validate([
            "name" => "required|string",
            'phone_number' => "required|string",
            "address"=> "required|string",
            "consult_price" => "numeric|min:0",
            "experience_description" => "string",
			"image" => "required"
        ]);
		
		   if($request->file('image')){
            $file= $request->file('image');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file-> move(public_path('/images'), $filename);
            $data['image']= $filename;
        }
		

        $expert->name = $data['name'] ;
        $expert->phone_number = $data['phone_number'];
        $expert->address = $data['address'];
        $expert->consult_price = $data['consult_price'];
		$expert->image = $data['image'];

		$oldExperiences = $expert->experiences()->get();
		  foreach($oldExperiences  as $experience){
			$experience->delete();
		  }
	
        $experience = new Experience();
        $experience->exp_description = $data['experience_description'];
        $experience->user()->associate($user);
        $experience->save();
		$expert->consults()->sync(explode(",", $request['consults']));

        $expert->save();
		$expert->consult_price = (int)$user->consult_price;
		$expert->isFavorite = false;
		

        return User::find($expert->id);
    }
	
	public function editNormalUserProfile(Request $request) {
		
        $user = Auth::user();
        $data = $request->validate([
            "name" => "required|string",
		    "image" => "required"
        ]);
		
        if($request->file('image')){
            $file= $request->file('image');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file-> move(public_path('/images'), $filename);
            $data['image']= $filename;
        }
		
		$user->name = $data['name']; 
		$user->image = $data["image"];
		$user->save();
        return $user;
    }
}
