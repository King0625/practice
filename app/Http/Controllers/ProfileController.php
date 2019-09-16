<?php

namespace App\Http\Controllers;

use App\Profile;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    

    /**
     * Display the specified resource.
     *
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $profile = Profile::where('user_id', $id)->first();
        if(!is_null($profile)){
            return response(['data' => $profile->get()]);
        }
        return response(['message' => 'User not found']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'first_name' => 'string|min:2|max:255',
            'last_name' => 'string|min:2|max:255',
            'age' => 'integer|min:0|max:150',
            'birthday' => 'date_format:Y-m-d',
            'gender' => 'string|min:1|min:1',
            'about_me' => 'string|min:1|max:1000',
            'privacy' => 'integer|min:0|max:2',
            'avatar' => 'file|image|max:5000'
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        
        // dd($request->avatar);

        if($request->hasFile('avatar')){
            $filenameWithExt = $request->file('avatar')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('avatar')->getClientOriginalExtension();
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            $path = $request->file('avatar')->storeAs('public/avatars', $fileNameToStore);
            $path_arr = explode('/', $path);
            $path_arr[0] = '/storage';
            $path = implode('/', $path_arr);
            // dd($path);
        }else{
            $path = '/storage/avatars/default.jpg';
        }
        $auth_user = request()->get('auth_user')->first();
        $profile = Profile::where('user_id', $id)->first();
        // dd($profile->user_id);
        if($auth_user['id'] == $profile->user_id){
            if(!is_null($profile)){
                $data = $request->all();
                $data['avatar'] = $path;
                $profile->update($data);
                return response(['data' => $profile->get()]);
            }else{
                return response(['message' => 'User not found!']);
            }
        }else{
            return response(['message' => 'Unauthorized!!']);
        }

    }
}
