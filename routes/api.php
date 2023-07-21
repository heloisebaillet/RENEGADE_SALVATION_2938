<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlanetarySystemController;
use App\Http\Controllers\RessourcesController;
use App\Http\Controllers\StructureController;
use App\Http\Controllers\WarehouseController;
use App\Models\Battle;
use App\Models\User;
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


/*Routes Protegés par le middleware*/

Route::middleware('jwt.verify')->group(function () {
    /* Route du système planétaire */
    Route::post('/index', [PlanetarySystemController::class, 'create'])->name('planetary_system.create');
    Route::get('/system/', [PlanetarySystemController::class, 'read'])->name('planetary_system.read');
    Route::put('/index', [PlanetarySystemController::class, 'update'])->name('planetary_system.update');

    /* Routes des infrastructures */
    Route::post('/structures/{type?}', [StructureController::class, 'create'])
        ->name('structures.create');
    Route::get('/structures/', [StructureController::class, 'read'])
        ->name('structures.read');
    Route::put('/structures/{id?}', [StructureController::class, 'addlevel'])
        ->name('structures.addlevel');
    Route::delete('/structures/{id?}', [StructureController::class, 'delete'])
        ->name('structures.delete');


    /* Route des Ressources  */
    Route::post('ressources/', [RessourcesController::class, 'create'])
    ->name('ressources.create');
    Route::get('ressources/', [RessourcesController::class, 'read'])
    ->name('ressources.read');
    Route::put('ressources/{type?}/{operation?}/{qty?}', [RessourcesController::class, 'update'])
    ->name('ressources.update');

    /* Routes des entrepôts */
    Route::post('/warehouses/', [WarehouseController::class, 'create'])
        ->name('warehouses.create');
    Route::get('/warehouses/', [WarehouseController::class, 'read'])
        ->name('warehouses.read');
    Route::put  ('/warehouses/', [WarehouseController::class, 'update'])
        ->name('warehouses.update');
    Route::delete('/warehouses/', [WarehouseController::class, 'delete'])
        ->name('warehouses.delete');

    /* Route des battles */
    Route::post('/battle', [BattleController::class, 'create'])
        ->name('battle.create');
    Route::get('/battle/', [BattleController::class, 'read'])
        ->name('battle.read');

});

/* Route du controller AuthController avec JWT  */
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

