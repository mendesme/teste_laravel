<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SeriesFormRequest;
use App\Models\Serie;
use App\Repositories\SerieRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    public function index(Request $request)
    {
        /*  ### Design de APIs
        
            Vamos fazer um filtro na nossa busca: http://localhost:800/api/series?nome=NomeQualquer
        
            poderíamos inclusive implementar uma ordenação : http://localhost:800/api/series?nome=NomeQualquer&order_by[asc]=nome

            ** o designt da API depende de você **        
        */


        /*  ### OBS: Vamos refatorar este bloco abaixo para um 'query builder'

        if (!$request->has('nome')) {

            return Serie::all();
        }

        $nome = $request->nome;                                 // ou  '$request->get('nome')' ou '$request->query('nome')'

        // return Serie::where('nome', $nome)->get();
        return Serie::whereNome($nome)->get();                  // Método mágico, equivalente a sintaxe acima
        
        */

        $query = Serie::query();

        if ($request->has('nome')) {

            $query->where('nome', $request->nome);
        }

        // return $query->get();
        return $query->paginate(3);                         // paginação com Laravel é moleza. Se precisarmos de paginação em uma aplicação fullstack, podemos usar exatamente o mesmo método

        /* ### append 'manual'                              // HATEOAS (ver conceito no model de Serie)
        return response()->json([

            'data' => $query->get(),

            'is_calopsita' => 'no'
        ]);
        */
    }

    public function store(SeriesFormRequest $request, SerieRepository $repository)
    {
        // $serie = Serie::create($request->all());        // poderia retornar diretamente o objeto serie (acima), o Laravel sabe tratar json. No entanto, é mais semântico usar uma response, e ainda mais semântico informar que se trata de um json

        if ($request->file('cover')) {

            $filePath = $request->file('cover')->store('series_cover', 'public');
            $request->filePath = $filePath;
        }

        $serie = $repository->add($request);

        return response()->json($serie, 201);
    }

    public function show(int $serie)
    {
        // return $serie;                      // caso queira apena essa entidade, só passar a classe 'Serie' no argumento

        // $serieWithRelations = Serie::with('seasons.episodes')->find($serie);
        $serieWithRelations = Serie::whereId($serie)->with('seasons.episodes')->first();    // Mesma query acima, mas talvez um pouco mais performática. Note o 'first()', o 'get()' traria uma lista

        return $serieWithRelations ?: response()->json(['message' => 'Series not found'], 404);
    }

    public function update(Serie $serie, SeriesFormRequest $request)    // não há necessidade de se declarar a $request, pois não precisaremos dela. No entanto, pode ser útil para fazer VALIDAÇÃO da mesma.
    {
        $serie->fill($request->all());
        $serie->save();

        return $serie;

        // Serie::where(‘id’, $series)->update($request->all());        // 1 query a menos dessa maneira
    }

    public function destroy(int $serie, Request $request, Authenticatable $user)
    {
        /*
        return response()->json(                            

            $request->user()->tokenCan('series:delete')
        );
        return response()->json($user);
        */

        // $serie->delete();
        Serie::destroy($serie);         // melhor performance

        // return response('', 204);
        return response()->noContent();
    }
}
