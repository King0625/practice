<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class CategoryController extends Controller
{
    public function index(){
        return response(['data' => Category::all() , 'Code' => 200]);
    }

    public function productIndex($id){
        $category = Category::find($id);
        if(!is_null($category)){
            return response(['data' => $category->products()->get() , 'Code' => 200]);
        }
        return response(['message' => 'Category not found']);
    }

    public function store(Request $request){
        $auth_user = request()->get('auth_user')->first();
        $items = ['Bowman', 'Thief', 'Warrior', 'Magician', 'Pirate', 'Beginner', 'Weapon', 'Armor', 'Accessory'];
        // $rules = [
        //     'name' => 'required|min:2|max:255'
        // ];
        // $validator = Validator::make($request->all(), $rules);
        // if($validator->fails()){
        //     return response()->json($validator->errors(), 400);
        // }
        

        if(!$auth_user['superuser']){
            return response(['message' => 'Permission denied!']);
        }
        // $data = $request->all();
        // $category = Category::create($data);
        // return response(['data' => $category , 'Code' => 201]);

        foreach($items as $item){
            Category::create(['name' => $item]);
        }

        return response(['data', Category::all()]);
    }
}
