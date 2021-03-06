<?php

namespace App\Http\Controllers;

use App\Category;
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
        return response()->json(['data' => Product::with('user', 'categories')->get()], 200);
        // return response()->json(['data' => Product::all()], 200);
    }


    public function userProducts($user_id){
        $user = User::find($user_id);
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
            'quantity' => 'required|integer|min:1|max:50',
        ];
        // dd($request->only(['name', 'description', 'price', 'quantity']));

        $validator = Validator::make($request->all(), $rules);
        // dd($rules);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $data = $request->only(['name', 'description', 'price', 'quantity']);
        $data['available'] = Product::AVAILABLE;
        $data['user_id'] = $auth_user['id'];

        // dd($data);
        if($auth_user['superuser']){
            $product = Product::create($data);

            $category1 = $request->input('cat1');
            $category2 = $request->input('cat2');
            $categories = Category::find([$category1, $category2]);
            if(!$category1){
                return response(['message' => 'You must assign at least one category']);
            }
            
            $product->categories()->attach($categories);

            return response()->json(['data' => $product, 'categories' => $product->categories()->get(), 'seller' => User::find($data['user_id'])], 201);
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
            'description' => 'max:1000',
            'price' => 'numeric|min:1|max:99.99',
            'quantity' => 'integer|min:1|max:50',
        ];
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        // $this->authorize('update', $product);
        if(!is_null($product)){
            if($auth_user->can('update', $product)){
                $product->update($request->all());
                return response()->json(['updated' => $product], 200);
            }else{
                return response(['message' => 'Permission denied']);
            }
        }else{
            return response()->json(['message' => 'Product not found!'], 404);
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
        $product = Product::find($id);

        if(!is_null($product)){
            if($auth_user->can('delete', $product)){
                $product->delete();
                return response()->json(['message' => 'Product deleted'], 200);
            }else{
                return response(['message' => 'Permission denied']);
            }
        }else{
            return response()->json(['message' => 'Product not found!'], 404);
        }

    }
}
