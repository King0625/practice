<?php

namespace App\Http\Controllers;

use App\Product;
use App\User;
use App\WishList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WishListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        $auth_user = request()->get('auth_user')->first();

        if($auth_user['superuser'] or $auth_user['id'] == $user->id){
            return response(['data' => $user->wishlist()->get()]);
        }
        return response(['message' => 'Permission denied!', 'code' => 403]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product)
    {
        $auth_user = request()->get('auth_user')->first();
        // dd($auth_user);
        $rules = [
            'quantity' => 'required|integer|min:1',
        ];
        $validator = Validator::make($request->all(), $rules);
        // Not "failed()" !!!
        if($validator->fails()){
            return response(['message' => $validator->errors()]);
        }

        $data = $request->all();
        $data['product_id'] = $product->id;
        $data['product_name'] = $product->name;
        $data['single_price'] = $product->price;
        $data['user_id'] = $auth_user->id;
        
        // dd($product->quantity);
        if($data['quantity'] <= $product->quantity){
            $wishlist = WishList::create($data);
            return response(['data' => $wishlist, 'code' => 201]);
        }
        return response(['message' => 'Not enough in stock']);

    }

    public function destroy(User $user, WishList $wishlist)
    {
        $auth_user = request()->get('auth_user')->first();
        $wishlist = $user->wishlist()->find($wishlist->id);
        // dd($wishlist);
        if($auth_user['id'] == $user->id){
            $wishlist->delete();
            return response(['message' => 'The wished item deleted', 'code' => 204]);
        }
        return response(['message' => 'Permission denied', 'code' => 403]);
    }
}
