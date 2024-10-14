<?php

namespace App\Http\Controllers\private\license;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CreateController extends Controller
{
    public function index(Request $request)
    {
        $validation = $this->validateRequest($request);

        if ($validation->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validation->errors()
            ], 400);
        }

        $validatedData = $validation->validated();
        $this->createLicense($validatedData);

        return response()->json([
            "success" => true,
            "message" => "License created successfully"
        ], 200);
    }

    private function validateRequest(Request $request)
    {
        return Validator::make($request->all(), $this->validationRules());
    }

    private function validationRules()
    {
        return [
            "product_name" => "required|min:1|max:32|string|starts_with:mv|regex:/.*_.*|exists:products,name",
            "guild_id" => "required|min:18|max:18|string",
            "user_id" => "required|min:18|max:18|string",
            "expires_at" => "date"
        ];
    }

    private function generateToken()
    {
        do {
            $token = strtoupper(Str::random(16));
            $token = preg_replace('/[^A-Z0-9]/', '', $token);
        } while (strlen($token) < 16);

        return $token;
    }

    private function createLicense($validatedData)
    {
        $productId = $this->getProductIdByName($validatedData['product_name']);

        License::create([
            "user_id" => $validatedData["user_id"],
            "product_id" => $productId,
            "guild_id" => $validatedData["guild_id"],
            "expires_at" => $validatedData["expires_at"] ?? null
        ]);
    }

    private function getProductIdByName($productName)
    {
        return Product::where('name', $productName)->pluck('id')->first();
    }
}
