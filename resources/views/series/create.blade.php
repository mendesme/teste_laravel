<x-layout title="Nova Série">

    <x-form :action="route('series.store')" :nome="old('nome')" :update="false" /> <!-- o ':' tbm é um short echo -->
    <!-- old() é uma função do Laravel que pega algo da request antiga-->
    
    <!-- 
    <form action="{{ route('series.store') }}" method="POST">
        @csrf
        <div class="mb-3">            
            <label for="nome" class="form-label">Nome:</label>
            <input type="text" name="nome" id="nome" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Adicionar</button>
    </form> 
    -->

</x-layout>