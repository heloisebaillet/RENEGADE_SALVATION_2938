<?php

use App\Http\Controllers\StructureController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
/* Routes des infrastructures*/
Route::post('/structures/{type?}',[StructureController::class, 'create'])->name('mines.create');
Route::get('/structures/{type?}',[StructureController::class, 'read'])->name('mines.read');
Route::put('/structures/{id?}',[StructureController::class, 'addlevel'])->name('mines.addlevel');
Route::delete('/structures/{id?}',[StructureController::class, 'delete'])->name('mines.delete');
