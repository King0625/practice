<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function searchProducts(Request $request){
        $search = $request->input('search');
        $products = Product::where('name', 'like', '%' . $search . '%')->with('user')->get();
        if(!count($products)){
            return response(['message' => 'Query not found!']);
        }
        return response(['data' => $products]);
    }
}
