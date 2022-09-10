<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->only('email', 'password');

        /*
        $user = User::whereEmail($credentials['email'])->first();

        if (
            $user === null ||
            Hash::check($credentials['password'], $user->password) === false
        ) {

            return response()->json('Unauthorized', 401);
        }
        */

        if (!Auth::attempt($credentials)) {                                 // Equivalente ao código acima

            return response()->json('Unauthorized', 401);     // Estamos utilizando a facade que salva em SESSION mas eu NÃO utilizo essa session em outra requisição. Sem problemas
        }

        /** @var User */
        $user = Auth::user();

        // $token = $user->createToken('token');
        $token = $user->createToken('token', ['series:delete']);
        /*
            o segundo parametro do createToken é um 'array de habilidades'. Podemos dar qualquer nome para ele. 
            ex: ['pode_remover_series'], mas é boa prática adotar ['recurso:operacao'].

            Cuidado, todo token por padrão (sem informar o segundo parametro) é ['*'], ou seja, pode tudo      
        
        */

        // return response()->json($user, 200);
        return response()->json(['token' => $token->plainTextToken], 200);
    }
}



/* ##### Auth::attempt #####

The attempt method accepts an array of key / value pairs as its first argument. 
The values in the array will be used to find the user in your database table. 
So, in the example above, the user will be retrieved by the value of the email column. If the user is found, 
the hashed password stored in the database will be compared with the password value passed to the method 
via the array. 
If the two hashed passwords match an authenticated session will be started for the user.

*/



/* ##### Sanctum #####

É preciso ter a migration 'personal_access_tokens' criada

Sanctum armazena o token no banco de dados

*/



/* ### Logout

em APIs normalmente não se implementa logout, mas caso queira:

$user->tokens()->delete();      // revoga TODOS os tokens do usuário

*/

