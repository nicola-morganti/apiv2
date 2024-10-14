<?php

namespace App\Http\Controllers\private\product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeleteController extends Controller
{
    private const VALIDATION_RULES = [
        "product_name" => "required|min:1|max:32|string|starts_with:mv|regex:/.*_.*|exists:products,name",
    ];

    public function index(Request $request)
    {
        $validationResult = $this->validateRequest($request);

        if ($validationResult !== true) {
            return $this->createErrorResponse("Validator Failed", 422);
        }

        $validatedData = $validationResult;

        $product = Product::where('name', $validatedData['product_name'])->first();
        $product->delete();

        return $this->createSuccessResponse("Deleted Product");
    }

    private function validateRequest(Request $request)
    {
        $validator = Validator::make($request->all(), self::VALIDATION_RULES);

        if ($validator->fails()) {
            return false;
        }

        return $validator->validated();
    }

    private function createErrorResponse(string $message, int $statusCode)
    {
        return response()->json([
            "success" => false,
            "message" => $message
        ], $statusCode);
    }

    private function createSuccessResponse(string $message)
    {
        return response()->json([
            "success" => true,
            "message" => $message
        ], 200);
    }
}
