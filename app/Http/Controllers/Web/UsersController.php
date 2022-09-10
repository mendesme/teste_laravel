<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsersController
{
    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $data = $request->except(['_token']);                   // token do form do Laravel
        $data['password'] = Hash::make($data['password']);      // mais uma FACILIDADE do FACADE. Ele já se encarrega do algoritmo

        $user = User::create($data);                            // usuário pronto e cadastrado!

        Auth::login($user);                                     // Posso usar mais uma facilidade aqui. Ao invés de mandar ele para o login e pedir para que se autentique,
        return redirect()->route('series.index');               // já posso autenticá-lo e enviá-lo para outra rota.
    }
}