<x-layout title="Login">

    @if($errors->any())<!-- errors é uma variável criada automaticamente pelo proprio Laravel -->
    <div class="alert alert-danger">
        <ul>
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
        </ul>
    </div>
    @endif

    <form method="POST">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" name="email" id="email" class="form-control">
        </div>
        <div class="form-group">
            <label for="password" class="form-label">Senha</label>
            <input type="password" name="password" id="password" class="form-control">
        </div>
        <button class="btn btn-primary mt-3">Entrar</button>
    </form>

    <a href="{{ route('users.create') }}" class="mt-3">Não possui casdastro? Clique aqui</a>

</x-layout>