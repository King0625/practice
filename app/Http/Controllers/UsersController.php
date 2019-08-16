<?php

namespace App\Http\Controllers;

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
    public function index()
    {
        $api_token = request()->header('api_token');

        // Check if the user(with that token) is superuser 
        $user = User::where('api_token', $api_token)->where('superuser',1)->get();
        if(count($user)){
            return response()->json(User::get(), 200);
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
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'max:12', 'confirmed'],
            'superuser' => ['required', 'boolean'],
            'api_token' => ['required', 'string']
        ];
        $validator = Validator::make($request->all(), $rules);
        if($validator->failed()){
            return response()->json($validator->errors(), 400);
        }

        // $user = User::create($request->all());
        $user = User::create([
            'name' => request('name'),
            'email' => request('email'),
            'password' => Hash::make(request('password')),
            'superuser' => request('superuser'),
            'api_token' => Str::random(60),
        ]);
        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $api_token = request()->header('api_token');
        // Check if the user(with that token) is superuser 
        $user = User::where('api_token', $api_token)->first();
        // dd($user->id);
        if($user->id == $id or $user->superuser == 1){
            if($this->exist($id)){
                $user = User::find($id);
                return response()->json($user, 200);
            }
            return response()->json(['message' => 'User not found!!'], 404);            
        }
        return response()->json(['message' => 'Request error!!'], 400);

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
            'name' => ['string', 'min:2', 'max:255'],
            'email' => ['string', 'email', 'max:255', 'unique:users'],
            'password' => ['string', 'min:6', 'max:12', 'confirmed'],
            'superuser' => ['boolean'],
        ];
        $validator = Validator::make($request->all(), $rules);
        if($validator->failed()){
            return response()->json($validator->errors(), 400);
        }

        $api_token = request()->header('api_token');
        // Check if the user(with that token) is superuser 
        $user = User::where('api_token', $api_token)->first();

        if($user->id == $id){
            $user = User::find($id);
            
            $user->update($request->all());
            return response()->json($user, 200);
        }

        return response()->json(['message' => 'Request error'], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $api_token = request()->header('api_token');
        // Check if the user(with that token) is superuser 
        $user = User::where('api_token', $api_token)->first();
        
        if($user->superuser == 1){
            if($this->exist($id)){
                $api_token = request()->header('api_token');
                // Check if the user(with that token) is superuser 
                $user = User::where('api_token', $api_token)->first();
                    $user = User::find($id);
                    $user->delete();
                    return response()->json(['message' => 'User deleted!!'], 204);
            }
            return response()->json(['message' => 'User not found!!'], 404);
        }
        return response()->json(['message' => 'Request error!!'], 400);
        
    }

    private function exist($id){
        $user = User::find($id);
        return !is_null($user);
    }
}
