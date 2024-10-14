<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class UpdateController extends Controller
{

    private function validateRequestHeaders($request)
    {
        $headers = $request->headers;

        if (empty($headers)) {
            return $this->jsonResponse(false, "No Headers provided", 400);
        }

        if ($headers["Secret-Token"] == config("api.auth.security.Secret-Token") && $headers["User-Agent"] == config("api.auth.security.User-Agent")) {
            return true;
        }

        return $this->jsonResponse(false, "Invalid Headers", 400);
    }
    private function jsonResponse($success, $message, $statusCode)
    {
        return response()->json([
            "success" => $success,
            "message" => $message,
        ], $statusCode);
    }
    public function index()
    {
        $headerValidation = $this->validateRequestHeaders($request);
        if ($headerValidation !== true) {
            return $headerValidation;
        }

        $validation = Validator::make($request->all(), [
            "product_name" => "required|min:1|max:32|string|starts_with:mv|regex:/.*_.*|exists:products,name",
            "license" => "required|min:22|max:22|string|starts_with:MVERSE|exists:license,token",
            "guild_id" => "required|min:18|max:18|string|exists:license,guild_id",
            "state" => "required|boolean|min:4|max:5"
        ]);

        if ($validation->fails()) {
            return $this->jsonResponse(false, $validation->errors(), 400);
        }

        $validatedData = $validation->validated();
        $productId = Product::where('name', $validatedData['product_name'])->pluck('id')->first();

        if (!$productId) {
            return $this->jsonResponse(false, "Product not found", 403);
        }

        $license = License::where('token', $validatedData['license'])
            ->where('product_id', $productId)
            ->where('guild_id', $validatedData["guild_id"])
            ->first();

        if (!$license) {
            return $this->jsonResponse(false, "License not found", 403);
        }

        $license->state = $validatedData["state"];
        $license->save();
        return $this->jsonResponse(true, "Updated State", 200);
    }
}
