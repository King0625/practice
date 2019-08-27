<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function searchProduct(Request $request){
        $search = $request->input('search');
        $products = Product::where('name', 'like', '%' . $search . '%')->first();
        if(!$products){
            return response(['message' => 'Query not found!']);
        }
        return response(['data' => $products]);
    }
}
