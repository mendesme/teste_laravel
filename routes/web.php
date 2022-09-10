<?php

use App\Http\Middleware\Autenticador;
use App\Mail\SeriesCreated;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\{
    EpisodesController,
    LoginController,
    SeasonsController,
    SeriesController,
    UsersController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    // return redirect('/series');
    return redirect()->route('series.index');
})->middleware(Autenticador::class);

/*

Route::get('/series', [SeriesController::class, 'index']);              // [Class, Método]
Route::get('/series/criar', [SeriesController::class, 'create']);       
Route::post('/series/salvar', [SeriesController::class, 'store']);      

*/

/**/
Route::controller(SeriesController::class)->group(function () {

    Route::get('/series', 'index')->name('series.index');

    Route::get('/series/criar', 'create')->name('series.create');       // Opcional: rotas nomeadas. São variáveis que armazenam a rota informada na url. Usamos a função 'route()' para chamar a rota. Ex: route('series.create') 
    Route::post('/series/salvar', 'store')->name('series.store');       // Qual a vantagem de se usar rotas nomeadas? Concentração em um único ponto, imagina se precisarmos mudar alguma rota.

    Route::get('/series/{serie}/editar', 'edit')->name('series.edit');
    Route::put('/series/{serie}/update', 'update')->name('series.update');

    // Route::post('/series/destroy/{serie}', 'destroy')->name('series.destroy');       // {param}
    Route::delete('/series/destroy/{serie}', 'destroy')->name('series.destroy');        // {param}

    Route::get('/series/{serie}/download', 'downloadCover')->name('series.download');
});



/*

Route::resource('/series', SeriesController::class)                   
    ->only('index', 'create', 'store', 'destroy');       

// Como nossas rotas estão NOMEADAS e utilizando os verbos PADRÕES, podemos também utilizar esta sintaxe AUTOMATICA.  
// o 'resource' cria TODAS as rotas, mesmo a que ainda não implementamos. Por boa prática, devemos usar o 'only' (ou 'except') para as definirmos.
// Cuidado com os PARAMETERS ele vai criar (no singular) de acordo com  o nome da rota.

*/



/* Dicas 

Route::get('/teste/{id?}', …)                                       // Optional Parameters

Route::get('/teste/{id}', …)->whereNumber('id');                    // Nós podemos impor restrições nos parâmetros que vamos enviar pelas URLs. Por exemplo, para o id sempre ser um número.

Route::get('/user/{name}', ...)->where('name', '[A-Za-z]+');        // whereAlpha | whereAlphaNumeric | whereUuid
Route::get('/user/{id}', ...)->where('id', '[0-9]+'); 
Route::get('/user/{id}/{name}', ...})->where(['id' => '[0-9]+', 'name' => '[a-z]+']);

*/


Route::controller(SeasonsController::class)->group(function () {

    Route::get('/series/{serie}/seasons', 'index')
        ->name('seasons.index')
        ->middleware('autenticador');                  // Usando um alias para o 'Autenticador::class'
});                                                    // Em: app\Http\Kernel.php -> protected $routeMiddleware

/*
Poderiamos utilizar '/seasons/{serie}' mas não é RESTFUL, uma vez que uma season é um 
SUBRECURSO de series, ou seja, não faz sentido de forma isolada
*/

Route::middleware('autenticador')->group(function () {  // Posso agrupar quantos controllers forem necessários sob um mesmo middleware

    Route::controller(EpisodesController::class)->group(function () {

        Route::get('/seasons/{season}/episodes', 'index')->name('episodes.index');
        Route::post('/seasons/{season}/episodes', 'update')->name('episodes.update');
    });
});

Route::controller(LoginController::class)->group(function () {

    Route::get('/login', 'index')->name('login');                   // padrão do Laravel
    Route::post('/login', 'store')->name('login.store');            // login
    Route::post('/logout', 'destroy')->name('login.destroy');        // logout
});

Route::controller(UsersController::class)->group(function () {

    Route::get('/users/registrar', 'create')->name('users.create');
    Route::post('/users/registrar', 'store')->name('users.store');
});


/*-------------------------------------------------------------------
    Rota teste de email
-------------------------------------------------------------------*/

Route::get('/email', function () {

    // return view('mail.series-created');
    return new SeriesCreated(
        'Teste',
        8,
        5,
        10
    );
});
