<?php

namespace App\Http\Controllers;

use App\Product;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(['data' => Product::all()], 200);
    }


    public function userProducts($id){
        $user = User::find($id);
        if(!is_null($user)){
            return response(['data' => $user->products()->get()]);
        }
        return response(['message' => 'User not found']);
        // dd($user->products()->get());
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $auth_user = request()->get('auth_user')->first();
        
        $rules = [
            'name' => 'required|min:2|max:255',
            'description' => 'required|max:1000',
            'price' => 'required|numeric|min:1|max:99.99',
            'quantity' => 'required|integer|min:1|max:50'
        ];
        $validator = Validator::make($request->all(), $rules);
        // dd($rules);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $data = $request->all();
        $data['available'] = Product::AVAILABLE;
        $data['user_id'] = $auth_user['id'];

        if($auth_user['superuser']){
            $product = Product::create($data);

            return response()->json(['data' => $data, 'seller' => User::find($data['user_id'])], 201);
        }
        return response()->json(['message' => 'Authentication error'], 401);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);
        if(!is_null($product)){
            return response()->json(['data' => $product], 200);
        }

        return response()->json(['message' => 'Product not found'], 404);
        
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
        $auth_user = request()->get('auth_user')->first();

        $product = Product::find($id);
        $rules = [
            'name' => 'min:2|max:255',
            'description' => 'required|max:1000',
            'price' => 'numeric|min:1|max:99.99',
            'quantity' => 'integer|min:1|max:50',
            'superuser' => 'boolean'
        ];
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        if($auth_user['superuser']){
            if(!is_null($product)){
                if($product->user_id = $auth_user['id']){
                    $product->update($request->all());
                    return response()->json(['updated' => $product], 200);
                }else{
                    return resposne(['message' => 'Permission denied']);
                }
            }else{
                return response()->json(['message' => 'Product not found!'], 404);
            }
        }
        return response()->json(['message' => 'Authentication error!'], 401);
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
        $product = Product::find($id);

        if($auth_user['superuser']){
            if(!is_null($product)){
                if($product->id = $auth_user['id']){
                    $product->delete();
                    return response()->json(['message' => 'Product deleted'], 200);
                }else{
                    return response(['message' => 'Permission denied']);
                }
            }else{
                return response()->json(['message' => 'Product not found!'], 404);
            }
        }
        return response()->json(['message' => 'Authentication error!'], 401);

    }
}
