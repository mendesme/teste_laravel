<?php

use App\Http\Controllers\Api\{LoginController, SeriesController};
use App\Models\Episode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
Route::get('/series', function(){                   // o Laravel já coloca 'api' na frente: http://localhost:8000/api/series

    return ['calopsita'=>'good'];
});
*/

Route::middleware('auth:sanctum')->group(function () {                  // API protegida por token. É necessário um token (ter feito login)

    Route::controller(SeriesController::class)->group(function () {

        Route::get('/series', 'index');                  // como não há links, não há necessicade de nomear
        Route::post('/series', 'store');
        Route::get('/series/{serie}', 'show');
        Route::put('/series/{serie}', 'update');
        Route::delete('/series/{serie}', 'destroy');
    });
});


// Route::apiResource('/series', SeriesController::class);          // equivalente acima. Use 'php artisan route:list' para conferir


/* Obs: não vou criar um controller por se tratar de um exemplo bem simples */
Route::patch('/episodes/{episode}/watch', function (Episode $episode, Request $request) {       // Poderia usar somente '/episodes/{episode}', mas acredito que seja legal explicitar. Poderia inclusive implementar um unwatch

    $episode->watched = $request->watched;
    $episode->save();

    return $episode;
});



Route::post('/login', [LoginController::class, 'store']);









/* APIs - Application Program Interface (*** Interface ***)

    APIs Web (1)
    APIs de código fonte

web app			 
mobile app	<---->  API <---->	    Database	
other APIs

cliente	    <----> GARÇOM <---->	cozinha

Padrões APIs web:
    RPC
    Soap (xml)
    REST

Normalmente ou um projeto é fullstack(1) ou api(2), mas nada impede de usar os dois
    (1) retorna html para o browser
    (2) retorna informações parseadas, normalmente json

*/

/* REST - Representational State Transfer

Padrão de transferência de RECURSOS

*/
