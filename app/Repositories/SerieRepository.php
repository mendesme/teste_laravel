<?php

namespace App\Repositories;

use App\Http\Requests\SeriesFormRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Serie;

class SerieRepository
{
    public function add(SeriesFormRequest $request): Serie
    {
        return DB::transaction(function () use ($request) {

            // $serie = Serie::create($request->all());
            $serie = Serie::create([

                'nome' => $request->nome,
                'cover' => $request->filePath
            ]);

            for ($s = 0; $s <= $request->seasonsQty; $s++) {

                $season = $serie->seasons()->create([

                    'numero' => $s
                ]);

                for ($e = 0; $e < $request->episodesPerSeason; $e++) {

                    $season->episodes()->create([

                        'numero' => $e
                    ]);
                }
            }

            return $serie;
        });
    }
}
