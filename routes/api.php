<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlanetarySystemController;
use App\Http\Controllers\RessourcesController;
use App\Http\Controllers\ShipsController;
use App\Http\Controllers\StructureController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\BattleController;
use App\Http\Controllers\ForgetPasswordController;
use App\Http\Controllers\RoundController;
use App\Http\Controllers\ShipyardController;
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
    /* Désactivation de la route create
    /*Route::post('/index', [PlanetarySystemController::class, 'create'])->name('planetary_system.create'); */
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

    /* Routes des Vaisseaux */
    Route::put('/ships/{type}/{operand}/{nbr_minus?}', [ShipsController::class, 'update'])
        ->name('ships.update');
    Route::get('/ships', [ShipsController::class, 'read'])
        ->name('ships.read');
    /* Routes des Shipyard */
    Route::get('/shipyard/', [ShipyardController::class, 'read'])
        ->name('shipyard.read');
    Route::get('/shipyard/available/', [ShipyardController::class, 'vacant'])
        ->name('shipyard.read_vacant');

    /* Route des Ressources  */
    /* Désactivation de la route create
    /*Route::post('ressources/', [RessourcesController::class, 'create'])
    /*->name('ressources.create');
    */
    Route::get('ressources/', [RessourcesController::class, 'read'])
        ->name('ressources.read');
    Route::put('ressources/{type?}/{operation?}/{qty?}', [RessourcesController::class, 'update'])
        ->name('ressources.update');
        Route::put('ressources/100', [RessourcesController::class, 'stripe'])
        ->name('ressources.update');

    /* Route du controller RjoundController*/
    Route::get("/round", [RoundController::class, "read"]);


    /* Routes des entrepôts */
    /* Désactivation de la route create
    /* Route::post('/warehouses/', [WarehouseController::class, 'create']) 
     /*   ->name('warehouses.create');*/
    Route::get('/warehouses/', [WarehouseController::class, 'read'])
        ->name('warehouses.read');
    Route::put('/warehouses/', [WarehouseController::class, 'update'])
        ->name('warehouses.update');
    Route::delete('/warehouses/', [WarehouseController::class, 'delete'])
        ->name('warehouses.delete');

    /* Route des battles */
    Route::post('/battle/{id?}', [BattleController::class, 'create'])
        ->name('battle.create');
    Route::get('/battle', [BattleController::class, 'read'])
        ->name('battle.read');
    Route::post('/attack', [BattleController::class, 'attack'])
        ->name('battle.attack');
    Route::get('/planetary-systems', [PlanetarySystemController::class, 'index1']);
});

/* Route du controller AuthController avec JWT  */
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');

    Route::match(['get', 'post'], 'update', 'updateProfile');

    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');

    Route::delete('/delete/', 'destroy');
});


/* Route de Controller ForgetPassword */
Route::get("/forget-password", [ForgetPasswordController::class, "forgetPassword"])
    ->name('forget.password');
Route::post("/forget-password", [ForgetPasswordController::class, "forgetPasswordPost"])
    ->name('forget.password.post');
Route::get("/reset-password/{token}", [ForgetPasswordController::class, "resetPassword"])
    ->name('reset.password');
Route::post('/reset-password/', [ForgetPasswordController::class, 'resetPasswordPost'])
    ->name('reset.password.post');
