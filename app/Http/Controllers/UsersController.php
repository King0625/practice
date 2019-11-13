<?php

namespace App\Http\Controllers;

use App\Profile;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class UsersController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'max:12', 'confirmed'],
        ]);
        
        
        $data['password'] = Hash::make($data['password']);
        $data['superuser'] = User::REGULAR_USER;
        $data['api_token'] = Str::random(60);
        $user = User::create($data);

        $profile_data = $request->only(['username']);
        $profile_data['user_id'] = $user->id;
        Profile::create($profile_data);
        return response()->json(['message' => 'Register successfully', 'data' => $user, 'api_token' => $user->api_token], 201);

    }


    public function login(Request $request){
        $credentials = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
        if(Auth::attempt($credentials)){
            $user = Auth::user();
            return response()->json(['message' => 'Login successfully','credential' => User::credential($user->superuser) , 'api_token' => $user->api_token, 'token type' => "Bearer"]);

        }
        return response(['message' => 'Login failed! Please check email or password!'], 401);

    }

    public function index()
    {
        $auth_user = request()->get('auth_user')->first();
        // dd($auth_user['superuser']);
        // Check if the user(with that token) is superuser 
        if($auth_user['superuser']){
            return response()->json(['data' => User::get()], 200);
        }
        return response()->json(['message' => 'Request forbidden'], 403);
        
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if(is_null($user)){
            return response(['message' => 'User not found!'], 404);
        }

        $auth_user = request()->get('auth_user')->first();
        // dd($auth_user['id'] == $user->id); 
        if(!($auth_user['id'] == $user->id)){
            return response(['message' => 'Request forbidden!'], 403);
        }

        $data = $request->validate([
            'username' => 'string|min:2|max:255',
            'email' => 'email|max:256|unique:users,email,'.$user->id,
            'password' => 'string|min:6|max:12|confirmed',
        ]);

        $user->update($data);
        return response()->json(['data' => $user], 200);

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
