<?php

namespace App\Http\Controllers;

use App\Product;
use App\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RatingController extends Controller
{
    
    public function productRatings(Product $product)
    {
        return response(["Ratings of '" . $product->name . "' from users" => $product->ratings()->with('user')->get(), 'Average rating' => $product->ratings()->avg('rating')]);
    }

    public function rateProduct(Request $request, Product $product)
    {
        $auth_user = request()->get('auth_user')->first();
        // dd($auth_user);
        $rules = [
            'rating' => 'required|numeric|min:0|max:5',
            'comment' => 'string'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return response(['message' => $validator->errors()]);
        }

        $data = $request->all();
        $data['product_id'] = $product->id;
        $data['user_id'] = $auth_user->id;

        $product_rated_by_user = $product->ratings()->where('user_id', $auth_user['id'])->first();
        // dd($product_rated_by_user);
        if(!is_null($product_rated_by_user)){
            return response(['message' => 'You have rated this product']);
        }
        
        $rating = Rating::create($data);
        return response(['data' => $rating, 'rated by' => $auth_user]);
    }

}
