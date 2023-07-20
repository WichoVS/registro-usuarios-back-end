<?php

use App\Http\Controllers\EntidadFederativaController;
use App\Http\Controllers\EstadoCivilController;
use App\Http\Controllers\GeneroController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post("/login", [UsersController::class, 'login']);

Route::get("/users/", [UsersController::class, 'getAll']);
Route::get("/users/{curp}", [UsersController::class, 'getUserByCURP']);
Route::post("/users/", [UsersController::class, 'create']);
Route::put("/users/", [UsersController::class, 'edit']);
Route::delete("/users/{curp}", [UsersController::class, 'delete']);


Route::get("/entidad-federativa/", [EntidadFederativaController::class, 'getAll']);
Route::get("/entidad-federativa/{id}", [EntidadFederativaController::class, 'getById']);
Route::post("/entidad-federativa/", [EntidadFederativaController::class, 'create']);
Route::put("/entidad-federativa/", [EntidadFederativaController::class, 'edit']);
Route::delete("/entidad-federativa/{id}", [EntidadFederativaController::class, 'delete']);

Route::get("/estado-civil/", [EstadoCivilController::class, 'getAll']);
Route::get("/estado-civil/{id}", [EstadoCivilController::class, 'getById']);
Route::post("/estado-civil/", [EstadoCivilController::class, 'create']);
Route::put("/estado-civil/", [EstadoCivilController::class, 'edit']);
Route::delete("/estado-civil/{id}", [EstadoCivilController::class, 'delete']);

Route::get("/genero/", [GeneroController::class, 'getAll']);
Route::get("/genero/{id}", [GeneroController::class, 'getById']);
Route::post("/genero/", [GeneroController::class, 'create']);
Route::put("/genero/", [GeneroController::class, 'edit']);
Route::delete("/genero/{id}", [GeneroController::class, 'delete']);