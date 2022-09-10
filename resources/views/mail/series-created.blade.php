@component('mail::message')

# {{ $nomeSerie }} criada com sucesso.

- {{ $nomeSerie }} com {{ $qtdTemporadas }} temporadas e {{ $episodiosPorTemporada }} episódios. 

Acesse aqui:

@component('mail::button', ['url' => route('seasons.index', $idSerie)])
Ver série
@endcomponent

@endcomponent

<!-- 

Cuidado com indentação - indentação em markdown é uma indicação de que queremos formatar um pedaço como código

# titulo

- lista

-->