<?php

namespace App\Http\Controllers;

use App\Product;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index(User $user){
        $auth_user = request()->get('auth_user')->first();
        // dd($user->transactions()->get());
        if($auth_user->superuser or $auth_user->id == $user->id){
            return response(['data', $user->transactions()->get()]);
        }
    }

    public function store(Request $request, Product $product){
        $auth_user = request()->get('auth_user')->first();
        // dd($auth_user);
        $rules = [
            'quantity' => 'required|integer|min:1'
        ];
        $validator = Validator::make($request->all(), $rules);
        // Not "failed()" !!!
        if($validator->fails()){
            return response(['message' => $validator->errors()]);
        }

        $data = $request->all();
        $data['product_id'] = $product->id;
        $data['product_name'] = $product->name;
        $data['price'] = $product->price;
        $data['total_price'] = $data['quantity'] * $data['price'];
        $data['user_id'] = $auth_user->id;
        // dd($product->quantity);
        if($data['quantity'] <= $product->quantity){
            $transaction = Transaction::create($data);
            $product->decrement('quantity', $data['quantity']);
            return response(['data' => $transaction, 'buyer' => $auth_user]);
        }
        return response(['message' => 'Not enough in stock']);


    }
}
