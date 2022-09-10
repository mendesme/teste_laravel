<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController               // UsersController? Melhor não, o login é algo 'temporário'. O user independe do login
{
    public function index()
    {
        return view('login.index');
    }

    public function store(Request $request)         // queremos (tentar) armazenar (temporariamente) um usuário no nosso sistema
    {
        if (!Auth::attempt($request->only('email', 'password'))) {       // os campos necessários para localizar nosso usuário. Se passar mais campos, ele procurará por eles, mas não há necessidade

            return redirect()->back()
                ->withErrors(['Usuário ou senha inválidos']);            // espécie de flash message
        }

        // se houver um 'Auth::attempt' ou 'Auth::login', conseguimos resgatar (conhecer) o user que está logado com
        //  'Auth::user()'

        return redirect()->route('series.index');
    }

    public function destroy()
    {
        Auth::logout();                 // Facade se encarrega de tudo
        
        return redirect()->route('login');
    }
}

/*
O Facade é uma FACHADA. Ele se encarrega de fazer tudo por debaixo dos panos:

    Auth::check() -> checa na session (e no backend) se há o cookie

    Auth::attempt() -> checa no banco de dados para ver se encontra o usuário fornecido

*/


/*

Nós já sabemos que o conceito de Facade é basicamente fornecer acesso simplificado a um subsistema mais complexo. 
É exatamente isso que a classe Auth nos fornece.

Ao chamar o método Auth::check ela verifica no guard configurado se há algum usuário presente. 
O guard padrão é session que armazena o usuário em sessão.

Quando chamamos o Auth::attempt passando as credenciais por parâmetro, o que o sistema vai fazer é utilizar o 
provider de usuários para tentar encontrar o usuário referente as credenciais enviadas. 
O provider padrão é o Eloquent, ou seja, nós tentamos buscar o usuário no banco de dados.

É possível personalizar isso usando um token ao invés de sessão e buscando o usuário em um servidor LDAP 
o invés de usar o Eloquent, mas para este treinamento as opções padrão são perfeitas. :-D

*/