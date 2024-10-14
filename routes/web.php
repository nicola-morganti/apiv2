<?php

use Illuminate\Support\Facades\Route;



Route::prefix("api")->group(function () {
    Route::prefix("auth")->group(function () {
        Route::post("validate", [\App\Http\Controllers\auth\ValidateController::class, "index"]);
        Route::post("update", [\App\Http\Controllers\auth\UpdateController::class, "index"]);
    });

    Route::prefix('private')->group(function () {
        // USER
        Route::get("user/view", [\App\Http\Controllers\private\user\ViewController::class, "index"]);
        Route::delete("user/clear", [\App\Http\Controllers\private\user\ClearController::class, "index"]);


        // LICENSE
        Route::post("license/create", [\App\Http\Controllers\private\license\CreateController::class, "index"]);
        Route::delete("license/delete", [\App\Http\Controllers\private\license\DeleteController::class, "index"]);
        Route::get("license/view", [\App\Http\Controllers\private\license\ViewController::class, "index"]);
        Route::post("license/give", [\App\Http\Controllers\private\license\GiveController::class, "index"]);
        Route::delete("license/remove", [\App\Http\Controllers\private\license\RemoveController::class, "index"]);


        // PRODUCT
        Route::post("product/create", [\App\Http\Controllers\private\product\CreateController::class, "index"]);
        Route::delete("product/delete", [\App\Http\Controllers\private\product\DeleteController::class, "index"]);
        Route::get("product/view", [\App\Http\Controllers\private\product\ViewController::class, "index"]);
        Route::get("product/list", [\App\Http\Controllers\private\product\ListController::class, "index"]);


    });
});
