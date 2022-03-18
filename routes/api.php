<?php
use App\Http\Controllers\V1\ProductsController;
use App\Http\Controllers\V1\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\ProveedoresController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */
Route::prefix('v1')->group(function () {
    //Prefijo V1, todo lo que este dentro de este grupo se accedera escribiendo v1 en el navegador, es decir /api/v1/*
    Route::post('login', [AuthController::class, 'authenticate']);
    Route::post('register', [AuthController::class, 'register']);
    Route::group(['middleware' => ['jwt.verify']], function() {
        //Todo lo que este dentro de este grupo requiere verificación de usuario.
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('get-user', [AuthController::class, 'getUser']);
        Route::put('user/updestuser/{id}', [AuthController::class, 'updEstUser']);
        Route::put('user/updpassuser/{id}', [AuthController::class, 'updPassUser']);
        Route::delete('user/eliminar/{id}', [AuthController::class, 'destroy']);
        //Productos
        Route::post('productos', [ProductsController::class, 'store']);
        Route::put('productos/{id}', [ProductsController::class, 'update']);
        Route::delete('productos/{id}', [ProductsController::class, 'destroy']);
        Route::get('productos', [ProductsController::class, 'index']);
        Route::get('productos/{id}', [ProductsController::class, 'show']);
        Route::get('productos/producto/{codigo}', [ProductsController::class, 'getProdCod']);
        Route::put('productos/producto/{codigo}', [ProductsController::class, 'ActProdCod']);
        Route::get('productos/linea/{linea}', [ProductsController::class, 'getProdxProvCod']);
        //Proveedor
        Route::post('proveedor', [ProveedoresController::class, 'store']);
        Route::put('proveedor/{id}', [ProveedoresController::class, 'update']);
        Route::delete('proveedor/{id}', [ProveedoresController::class, 'destroy']);
        Route::get('proveedor', [ProveedoresController::class, 'index']);
        Route::get('proveedor/{id}', [ProveedoresController::class, 'show']);
        Route::get('proveedor/proveedor/{codigo}', [ProveedoresController::class, 'getProvCod']);
        Route::put('proveedor/proveedor/{codigo}', [ProveedoresController::class, 'ActProvCod']);

        
    });
});

