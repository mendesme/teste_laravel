<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}


/*

Existe um conceito nos frameworks PHP de Exception Handler. São códigos que vão capturar exceções de forma global e tomar as ações necessárias.

Seria perfeitamente possível criar um exception handler para recuperar a exception de model não encontrada, 
mas o Laravel já facilita isso com um método de renderizar exceções.

Em app/Exceptions/Handler.php nós temos uma classe que seria a base para criarmos handlers, nela nós podemos
implementar um método chamado render que cuida de devolver uma resposta em caso de exceção.

Para termos um 404 retornado corretamente em JSON, poderíamos ter algo como:

    use Illuminate\Database\Eloquent\ModelNotFoundException;

    // ...

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException && $request->wantsJson()) {
            return response()->json(['message' => 'Not Found!'], 404);
        }

        return parent::render($request, $exception);
    }

*/