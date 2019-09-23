<?php

namespace App\Http\Controllers;

use App\Profile;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use function PHPSTORM_META\type;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request){
        $email = $request->auth_email;
        $password = $request->auth_password;

        $user = User::where('email', $email)->first();
        // dd($user);
        if(!$user){
            return response()->json(['message' => 'Login failed. Please check email id'], 401);
        }
        if(!Hash::check($password, $user->password)){
            return response()->json(['message' => 'Login failed. Please check password'], 401);
        }
        return response()->json(['message' => 'Login successfully', 'api_token' => $user->api_token]);
    }

    public function index()
    {
        $auth_user = request()->get('auth_user')->first();
        // dd($auth_user['superuser']);
        // Check if the user(with that token) is superuser 
        if($auth_user['superuser']){
            return response()->json(['data' => User::get()], 200);
        }
        return response()->json(['message' => 'Request error'], 400);
        
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
        $rules = [
            'username' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'max:12', 'confirmed'],
        ];
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $data['superuser'] = User::ADMIN_USER;
        $data['api_token'] = Str::random(60);
        // $user = User::create($request->all());
        $user = User::create($data);

        $profile_data = $request->only(['username']);
        $profile_data['user_id'] = $user->id;
        Profile::create($profile_data);
        return response()->json(['data' => $user, 'api_token' => $user->api_token], 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $auth_user = request()->get('auth_user')->first();
        $user = User::find($id);
        // dd($user->id);
        if($auth_user['superuser']){
            if($this->exist($user)){
                return response()->json(['data' => $user], 200);
            }else{
                return response()->json(['message' => 'User not found!!'], 404);            
            }
        }elseif($auth_user['id'] == $id){
            return response()->json(['data' => $user], 200);
        }else{
            return response()->json(['message' => 'Authentication error!!'], 401);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'username' => 'string|min:2|max:255',
            'email' => 'email|max:256|unique:users,email,'.$id,
            'password' => 'string|min:6|max:12|confirmed',
            'superuser' => 'boolean',
        ];
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $auth_user = request()->get('auth_user')->first();
        $user = User::find($id);
        // Check if the user(with that token) is superuser 

        if($auth_user['superuser']){
            if(($this->exist($user) && !$user['superuser']) || $auth_user['id'] == $id){
                $user->update($request->all());
                return response()->json(['data' => $user], 200);
            }else{
                return response()->json(['message' => 'User not found or that is a superuser!'], 404);
            }
        }elseif($auth_user['id'] == $id){
            $user->update($request->all());
            return response()->json(['data' => $user], 200);
        }else{
            return response()->json(['message' => 'Request error'], 400);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $auth_user = request()->get('auth_user')->first();
        $user = User::find($id);        
        if($auth_user['superuser']){
            if(($this->exist($user) && !$user['superuser']) || $auth_user['id'] == $id){
                $user->delete();
                return response()->json(['message' => 'User deleted!!'], 200);
            }else{
                return response()->json(['message' => 'User not found or that is a superuser'], 404);
            }
        }elseif($auth_user['id'] == $id){
            $user->delete();
            return response()->json(['message' => 'User deleted!!'], 200);
        }else{
            return response()->json(['message' => 'Request error!!'], 400);
        }
        
    }

    private function exist($id){
        $user = User::find($id);
        return !is_null($user);
    }
}
