<?php

namespace App\Http\Controllers\private\product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    private const SUCCESS_MESSAGE = "Fetched Products";
    private const RESPONSE_KEY_SUCCESS = "success";
    private const RESPONSE_KEY_MESSAGE = "message";
    private const RESPONSE_KEY_DATA = "data";
    private const RESPONSE_KEY_PRODUCTS = "products";

    public function index(Request $request)
    {
        $products = Product::all();
        return $this->createJsonResponse($products);
    }

    private function createJsonResponse($products)
    {
        return response()->json([
            self::RESPONSE_KEY_SUCCESS => true,
            self::RESPONSE_KEY_MESSAGE => self::SUCCESS_MESSAGE,
            self::RESPONSE_KEY_DATA => [
                self::RESPONSE_KEY_PRODUCTS => $products
            ]
        ], 200);
    }
}
