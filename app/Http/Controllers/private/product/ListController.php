<?php

namespace App\Http\Controllers\private\product;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ListController extends Controller
{
    private const VALIDATION_RULES = [
        "product_name" => "required|min:1|max:32|string|starts_with:mv|regex:/.*_.*|exists:products,name",
    ];

    public function index(Request $request)
    {
        $validator = $this->getValidator($request);

        if ($validator->fails()) {
            return $this->validationErrorResponse();
        }

        $validatedData = $validator->validated();
        $productId = $this->getProductIdByName($validatedData['product_name']);

        return response()->json([
            "success" => true,
            "message" => "fetched Data",
            "data" => [
                "licenses" => $this->getLicensesByProductId($productId)
            ]
        ], 200);
    }

    private function getValidator(Request $request)
    {
        return Validator::make($request->all(), self::VALIDATION_RULES);
    }

    private function validationErrorResponse()
    {
        return response()->json([
            "success" => false,
            "message" => "Validator failed"
        ], 422);
    }

    private function getProductIdByName($productName)
    {
        return Product::where('name', $productName)->pluck('id')->first();
    }

    private function getLicensesByProductId($productId)
    {
        return License::where('product_id', $productId)->get();
    }
}
