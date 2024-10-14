<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\LicenseLog;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidateController extends Controller
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

    private function formatDateTime($dateTime)
    {
        return Carbon::parse($dateTime)->format('d.m.Y - H:i');
    }

    private function jsonResponse($success, $message, $statusCode)
    {
        return response()->json([
            "success" => $success,
            "message" => $message,
        ], $statusCode);
    }

    public function index(Request $request)
    {
        // Validate headers
        $headerValidation = $this->validateRequestHeaders($request);
        if ($headerValidation !== true) {
            return $headerValidation;
        }

        // Validate request data
        $validation = Validator::make($request->all(), [
            "product_name" => "required|min:1|max:32|string|starts_with:mv|regex:/.*_.*|exists:products,name",
            "license" => "required|min:22|max:22|string|starts_with:MVERSE|exists:license,token",
            "guild_id" => "required|min:18|max:18|string|exists:license,guild_id"
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

        if (!$license->state) {
            return $this->jsonResponse(false, "License already active", 400);
        }else {
            $license->state = true;
            $license->save();
        }

        $expires_at = Carbon::parse(optional($license)->expires_at ?? "2300-01-01 12:00:00");
        $current_date = Carbon::now();
        if ($expires_at > $current_date) {
            LicenseLog::create([
                "license_id" => $license->id,
                "action" => "License authenticated by Guild " . $validatedData["guild_id"]
            ]);

            return response()->json([
                "success" => true,
                "message" => "Validated Successfully",
                "data" => [
                    "expires_at" => $expires_at == Carbon::parse("2300-01-01 12:00:00") ? "Lifetime" : $this->formatDateTime($expires_at)
                ]
            ], 200);
        }

        return $this->jsonResponse(false, "License expired", 403);
    }
}
