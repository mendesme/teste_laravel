<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    use HasFactory;

    protected $fillable = ['numero'];             // mass assignment
    public $timestamps = false;
    protected $casts = [                          // como se trata apenas de um cas simples, não há necessidade de  Acessors e Mutators 

        'watched' => 'boolean'
    ];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function scopeWatched(Builder $query)
    {
        return $query->where('watched', true);
    }

    /*  CASTS (conversões) - Acessors e Mutators   

    protected function watched(): Attribute
    {
        return new Attribute(

            fn (int $watched) : bool => $watched,           // get: quando vc ler o atributo watched, que é um inteiro no banco de dados, retorne (get) um booleano
            // fn (bool $watched) : int => $watched         // set: quando eu te passar um booleano, grave (set) como um inteiro
        );
    }

    */
}
