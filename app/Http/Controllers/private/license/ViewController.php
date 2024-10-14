<?php

namespace App\Http\Controllers\private\license;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ViewController extends Controller
{
    public function index(Request $request)
    {
        $validationResult = $this->validateLicense($request);

        if ($validationResult['fails']) {
            return $this->buildJsonResponse(false, $validationResult['message'], 400);
        }

        $validatedData = $validationResult['validatedData'];
        $license = License::where("token", $validatedData["license"])->first();

        if (!$license) {
            return $this->buildJsonResponse(false, "License not found", 404);
        }

        return $this->buildJsonResponse(true, ["license" => $license], 200);
    }

    private function validateLicense(Request $request)
    {
        $validation = Validator::make(
            $request->all(), [
                "license" => "required|min:22|max:22|string|starts_with:MVERSE|exists:license,token"
            ]
        );

        if ($validation->fails()) {
            return [
                'fails' => true,
                'message' => "Invalid Body",
                'validatedData' => []
            ];
        }

        return [
            'fails' => false,
            'message' => '',
            'validatedData' => $validation->validated()
        ];
    }

    private function buildJsonResponse($success, $data, $statusCode)
    {
        return response()->json([
            "success" => $success,
            "message" => is_string($data) ? $data : '',
            "data" => is_array($data) ? $data : []
        ], $statusCode);
    }
}
