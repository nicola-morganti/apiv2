<?php

namespace App\Http\Controllers\private\license;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeleteController extends Controller
{
    public function index(Request $request)
    {
        $validation = $this->validateRequest($request);

        if ($validation->fails()) {
            return $this->validationErrorResponse($validation);
        }

        $validatedData = $validation->validated();

        $this->deleteLicense($validatedData['license']);

        return $this->successResponse();
    }

    private function validateRequest(Request $request)
    {
        $rules = [
            "license" => "required|min:22|max:22|string|starts_with:MVERSE|exists:license,token",
        ];
        return Validator::make($request->all(), $rules);
    }

    private function validationErrorResponse($validation)
    {
        return response()->json([
            "success" => false,
            "message" => "Validation failed",
            "errors" => $validation->errors()
        ], 422);
    }

    private function deleteLicense($licenseToken)
    {
        $license = License::where('token', $licenseToken)->first();
        $license->delete();
    }

    private function successResponse()
    {
        return response()->json([
            "success" => true,
            "message" => "Successfully deleted License"
        ], 200);
    }
}
