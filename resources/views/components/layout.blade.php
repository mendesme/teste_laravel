<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Controle de Séries</title><!-- o {_{ $var }_} significar que é uma variável, será preenchido por outra coisa -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">

            <a href="{{ route('series.index') }}" class="navbar-brand">Home</a>

            @auth<!-- só exibe se o usuário estiver autenticado -->
            <form action="{{ route('login.destroy') }}" method="POST">@csrf
                <button class="btn btn-link">Sair</button>                
            </form><!-- uma requisição post é mais adequada a um logout -->
            @endauth

            @guest<!-- opção mais semantica do que um 'elseauth' -->
            <a href="{{ route('login') }}">Entrar</a>
            @endguest

        </div>
    </nav>  

    <div class="container">

        <h1>{{ $title }}</h1>

        @isset($mensagemSucesso)
        <div class="alert alert-success">
            {{ $mensagemSucesso}}    
        </div>
        @endisset
    
            {{ $slot }}

    </div>
</body>
</html>