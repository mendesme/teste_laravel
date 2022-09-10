<x-layout title="Séries">
    @auth
    <a href="{{ route('series.create') }}" class="btn btn-dark mb-2">Adicionar nova série</a>
    @endauth

    @isset($mensagemSucesso)
    <div class="alert alert-success">
        {{ $mensagemSucesso }}
    </div>
    @endisset

    <ul class="list-group">
        @foreach($series as $serie)

        <li class="list-group-item d-flex justify-content-between align-items-center"> 

            <div>
                <img src="{{ asset('storage/' . $serie->cover) }}" alt="Capa da série" 
                    class="img-thumbnail"
                    width="100"
                    >
                @isset($serie->cover)
                <a href="{{ route('series.download', $serie->id) }}">Download</a>
                @endisset
            </div>

            @auth<a href="{{ route('seasons.index', $serie->id) }}">@endauth
                {{ $serie->nome; }}<!-- o nome ele vê, não vê o LINK-->
            @auth</a>@endauth
            
            @auth
            <span class="d-flex">    
                
            <a href="{{ route('series.edit', $serie->id) }}" class="btn btn-primary btn-sm">E</a>
                <!-- 
                    1) Estamos pedindo (get) a uma rota para exibir uma view para depois editarmos.
                    2) a url de edit é: /series/{serie}/edit      //{serie} é o id  
                 -->
                <form action="{{ route('series.destroy', $serie->id ) }}" method="POST" class="ms-2">
                    @csrf
                    @method('DELETE')
                    <!-- por baixo dos panos o laravel passa a tratar como DELETE -->
                    <button class="btn btn-danger btn-sm">X</button>
                </form>
            </span>
            @endauth

        </li>

        @endforeach
    </ul>

</x-layout>