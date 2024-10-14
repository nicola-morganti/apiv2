<?php

namespace App\Http\Controllers\private\license;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GiveController extends Controller
{
    public function index(Request $request)
    {
        $validation = $this->validateRequest($request);

        if ($validation->fails()) {
            return $this->respondWithValidationError();
        }

        $validatedData = $validation->validated();
        $this->updateLicense($validatedData);

        return $this->respondWithSuccess("License updated successfully");
    }

    private function validateRequest(Request $request)
    {
        return Validator::make($request->all(), [
            "license" => "required|min:22|max:22|string|starts_with:MVERSE|exists:license,token",
            "guild_id" => "required|min:18|max:18|string"
        ]);
    }

    private function respondWithValidationError()
    {
        return response()->json([
            "success" => false,
            "message" => "Invalid Body"
        ], 400);
    }

    private function respondWithSuccess($message, $statusCode = 200)
    {
        return response()->json([
            "success" => true,
            "message" => $message
        ], $statusCode);
    }

    private function updateLicense($validatedData)
    {
        $license = License::where("token", $validatedData["license"])->first();
        $license->guild_id = $validatedData["guild_id"];
        $license->license_changes += 1;
        $license->save();
    }
}
