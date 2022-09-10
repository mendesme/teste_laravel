<x-layout title="Editar Série {!! $serie->nome !!}"><!-- sintaxe não segura -->

    <x-form :action="route('series.update', $serie->id)" nome="{{ $serie->nome }}" update="{{ true }}"/> <!-- Note a sintaxe -->
    <!--route('rota', 'parameter') -->
</x-layout>