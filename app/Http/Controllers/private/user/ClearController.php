<?php

namespace App\Http\Controllers\private\user;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClearController extends Controller
{
    public function index(Request $request)
    {
        $validation = $this->validateRequest($request);

        if ($validation->fails()) {
            return $this->validationErrorResponse();
        }

        $validatedData = $validation->validated();
        $this->clearLicenses($validatedData["user_id"]);

        return response()->json([
            "success" => true,
            "message" => "Cleared Licenses"
        ], 200);
    }

    private function validateRequest(Request $request)
    {
        return Validator::make($request->all(), [
            "user_id" => "required|min:18|max:18|string|exists:license,user_id",
        ]);
    }

    private function validationErrorResponse()
    {
        return response()->json([
            "success" => false,
            "message" => "Invalid Body"
        ], 400);
    }

    private function clearLicenses($userId)
    {
        $licenses = License::where("user_id", $userId)->get();
        $licenses->each(function ($license) {
            $license->delete();
        });
    }
}
