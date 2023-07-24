<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlanetarySystemController;
use App\Http\Controllers\RessourcesController;
use App\Http\Controllers\StructureController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\BattleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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


    /* Route des Ressources  */
    /* Désactivation de la route create
    /*Route::post('ressources/', [RessourcesController::class, 'create'])
    /*->name('ressources.create');
    */
    Route::get('ressources/', [RessourcesController::class, 'read'])
        ->name('ressources.read');
    Route::put('ressources/{type?}/{operation?}/{qty?}', [RessourcesController::class, 'update'])
        ->name('ressources.update');

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
    Route::get('/battle/{id?}/', [BattleController::class, 'read'])
        ->name('battle.read');
});

/* Route du controller AuthController avec JWT  */
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

/* Routes pour envoyer la demande de réinitialisation du mot de passe de l'utilisateur */
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);
    $status = Password::sendResetLink($request->only('email'));
    return $status === Password::RESET_LINK_SENT ? back()
        ->with(['status' => __($status)]) : back()
        ->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');

/* Route pour récupérer le token envoyé à l'utilisateur après la demande de réinitialisation */
Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

/* Route pour enregistrer le nouveau mot de passe envoyé par l'utilisateur depuis la view reset form */
Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => [__($status)]]);
})->middleware('guest')->name('password.update');
