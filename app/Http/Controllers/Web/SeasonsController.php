<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Season;
use App\Models\Serie;
use Illuminate\Http\Request;

class SeasonsController extends Controller
{
    public function index(Serie $serie)
    {
        // $seasons = $serie->seasons;
        $seasons = $serie->seasons()->with('episodes')->get();      // eager loading (diferente de lazy loading). Menos queries, pega as seasons COM seus episodes

        return view('seasons.index')
            ->with('seasons', $seasons)
            ->with('serie', $serie)
            ->with('mensagemSucesso', session('mensagem.sucesso'));
    }

    /*
    // Caso estejamos interessados em menos queries, podemos usar diretamente a classe 
    // 'Season'. Note que o parâmetro da nossa função index não é mais um objeto (o que exige mais uma query) e 
    // sim apenas um inteiro.

    public function index(int $serie)
    {
        $seasons = Season::query()
            ->where('series_id', $serie)
            ->with('episodes')
            ->get();

        return view('seasons.index')
            ->with('seasons', $seasons);
    }
    */
}
