@if($errors->any())
<!-- errors é uma variável criada automaticamente pelo proprio Laravel -->
<div class="alert alert-danger">
    <ul>
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ $action }}" method="POST" enctype="multipart/form-data"><!-- multipart informa que serão enviado files -->
    @csrf
    @if($update) @method('PUT') @endif

    <div class="row mb-3">

        <div class="col-8">
            <label for="nome" class="form-label">Nome:</label>
            <input type="text" autofocus name="nome" id="nome" class="form-control" @isset($nome) value="{!! $nome !!}" @endisset>
        </div>

        <div class="col-2">
            <label for="seasonsQty" class="form-label">Nº Temporadas:</label>
            <input type="text" name="seasonsQty" id="seasonsQty" class="form-control" value="{{ $nome }}">
        </div>

        <div class="col-2">
            <label for="episodesPerSeason" class="form-label">Episodios:</label>
            <input type="text" name="episodesPerSeason" id="episodesPerSeason" class="form-control" value="{{ $nome }}">
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <label for="cover" class="form-label">Capa</label>
                <input type="file" accept="image/gif, image/jpeg, image/png" 
                    id="cover" 
                    name="cover" 
                    class="form-control">
            </div>
        </div>

    </div>

    <button type="submit" class="btn btn-primary">Adicionar</button>

</form>