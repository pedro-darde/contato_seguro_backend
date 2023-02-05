<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get("/", function () {
   return "<h1> Contato Seguro API </h1>";
});


Route::get("/user", [UserController::class, 'list']);
Route::post("/user", [UserController::class, 'create']);
Route::patch('/user/{user}', [UserController::class, 'update']);
Route::delete('/user/{user}', [UserController::class, 'delete']);


Route::get("/company/{searchField?searchValue?searchOperation?}", [CompanyController::class, 'list']);
Route::post("/company", [CompanyController::class, 'create']);
Route::patch('/company/{company}', [CompanyController::class, 'update']);
Route::delete('/company/{company}', [CompanyController::class, 'delete']);