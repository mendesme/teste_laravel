<x-layout title="Episódios">

    <form method="POST"><!-- envia para a mesma url -->
        @csrf
        <ul class="list-group">
            @foreach($episodes as $episode)
            <li class="list-group-item d-flex justify-content-between align-items-center">    

                Episódio {{ $episode->numero; }}
                <input 
                    type="checkbox" 
                    name="episodes[]" 
                    value="{{ $episode->id }}"
                    @if($episode->watched) checked @endif>  
                        
            </li> <!-- name="var[]" value="value">  é uma sintaxe do PHP. Ele monta um array com os values -->
            @endforeach
        </ul>
        <button class="btn btn-primary mt-2 mb-2">Salvar</button>
    </form>

</x-layout>