<?php

namespace App\Http\Controllers\Web;

use App\Models\{Season, Episode};
use Illuminate\Http\Request;

class EpisodesController
{
    public function index(Season $season, Request $request)
    {
        return view('episodes.index')->with('episodes', $season->episodes);
    }

    public function update(Season $season, Request $request)
    {
        $watchedEpisodes = $request->episodes;

        $season->episodes->each(function (Episode $episode) use ($watchedEpisodes) {    // não seria melhor usar uma arrow function? 
            $episode->watched = in_array($episode->id, $watchedEpisodes);               // Aqui não, pois uma arrow function retornaria um valor a cada iteração, 
            // $episode->save();                                                        // e a função 'each' sai do loop no primerio false que ela encontrar
        }); // vamos usar o 'push()': ele salva o model e seus relacionamentos                                                                          
       
        $season->push();

        return  redirect()->route('seasons.index', $season->series_id)
        ->with('mensagem.sucesso', 'Episódios atualizados com sucesso');
    }
}
