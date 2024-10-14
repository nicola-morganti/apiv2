<?php

namespace App\Http\Controllers\private\product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreateController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "product_name" => "required|min:1|max:32|string|starts_with:mv|regex:/.*_.*",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();

        $product = new Product();
            $product->name = $validatedData['product_name'];
            $product->save();
        return response()->json([
            "success" => true,
            "message" => "Product created"
        ], 200);
    }
}
