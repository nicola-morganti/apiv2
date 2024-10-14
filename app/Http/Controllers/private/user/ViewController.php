<?php

namespace App\Http\Controllers\private\user;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ViewController extends Controller
{
    private const VALIDATION_RULES = [
        "user_id" => "required|min:18|max:18|string|exists:license,user_id",
    ];

    public function index(Request $request)
    {
        $validation = $this->validateRequest($request);

        if ($validation->fails()) {
            return $this->validationErrorResponse();
        }

        $validatedData = $validation->validated();

        return response()->json([
            "success" => true,
            "data" => [
                "licenses" => License::where("user_id", $validatedData["user_id"])->get(),
            ]
        ]);
    }

    private function validateRequest(Request $request)
    {
        return Validator::make($request->all(), self::VALIDATION_RULES);
    }

    private function validationErrorResponse()
    {
        return response()->json([
            "success" => false,
            "message" => "Invalid Body"
        ], 400);
    }
}
