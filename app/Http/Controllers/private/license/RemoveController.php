<?php

namespace App\Http\Controllers\private\license;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RemoveController extends Controller
{
    public function index(Request $request)
    {
        $validation = $this->validateRequest($request);
        if ($validation->fails()) {
            return $this->response(false, "Invalid Body", 400);
        }

        $validatedData = $validation->validated();
        $license = $this->findLicense($validatedData["license"]);
        if (!$license) {
            return $this->response(false, "License not found", 400);
        }

        $this->clearLicense($license);

        return $this->response(true, "Removed License", 200);
    }

    private function validateRequest(Request $request)
    {
        return Validator::make($request->all(), [
            "license" => "required|min:22|max:22|string|starts_with:MVERSE|exists:license,token"
        ]);
    }

    private function findLicense($token)
    {
        return License::where("token", $token)->first();
    }

    private function clearLicense($license)
    {
        $license->guild_id = null;
        $license->user_id = null;
        $license->save();
    }

    private function response($success, $message, $statusCode)
    {
        return response()->json([
            "success" => $success,
            "message" => $message
        ], $statusCode);
    }
}
