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

        return response([
            'message' => 'Register successfully',
            'data' => $user,
            'api_token' => $user->api_token
        ], 201);

    }


    public function login(Request $request){
        $credentials = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        if(Auth::attempt($credentials)){
            $user = Auth::user();
            return response([
                'message' => 'Login successfully',
                'credential' => User::credential($user->superuser),
                'api_token' => $user->api_token,
                'token type' => "Bearer"
            ]);
        }

        return response([
            'message' => 'Login failed! Please check email or password!'
        ], 401);

    }

    public function index()
    {
        $auth_user = request()->get('auth_user')->first();

        if($auth_user['superuser']){
            return response([
                'data' => User::get()
            ]);
        }

        return response([
            'message' => 'Request forbidden'
        ], 403);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        if(is_null($user)){
            return response([
                'message' => 'User not found!'
            ], 404);
        }

        $auth_user = request()->get('auth_user')->first();

        if(!($auth_user['superuser'] || $auth_user['id'] == $id)){
            return response([
                'message' => 'Request forbidden!'
            ], 403);
        }

        return response([
            'data' => $user
        ]);

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
            return response([
                'message' => 'User not found!'
            ], 404);
        }

        $auth_user = request()->get('auth_user')->first();

        if(!($auth_user['id'] == $user->id)){
            return response([
                'message' => 'Request forbidden!'
            ], 403);
        }

        $data = $request->validate([
            'username' => 'string|min:2|max:255',
            'email' => 'email|max:256|unique:users,email,'.$user->id,
            'password' => 'string|min:6|max:12|confirmed',
        ]);

        $user->update($data);

        return response([
            'data' => $user
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);        

        if(is_null($user)){
            return response([
                'message' => 'User not found!'
            ], 404);
        }

        $auth_user = request()->get('auth_user')->first();

        if(!$auth_user['id'] == $id){
            return response([
                'message' => 'Request forbidden'
            ], 403);
        }

        if($user->superuser){
            return response([
                'message' => 'Cannot delete a superuser!'
            ], 403);
        }

        // To delete a user, you should delete his(or her) profile first
        $profile = Profile::where('user_id', $id)->first();
        $profile->delete();
        $user->delete();

        return response([
            'message' => 'User deleted!!'
        ], 200);
        
    }
}
