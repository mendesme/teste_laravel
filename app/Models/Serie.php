<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Null_;

class Serie extends Model
{
    use HasFactory;                       // trait

    // public $timestamps = false;
    // protected $primaryKey = 'id';    // já é padrão 

    protected $fillable = ['nome', 'cover'];             // necessário para se usar o mass assignment ('Serie::create($request->all());')    
    // protected $with = ['seasons'];                    // contrário do 'lazy loading'
    protected $appends = ['links'];                      // HATEOAS

    public function seasons()                            // O Eloquent trabalha com FUNÇÕES DE RELACIONAMENTO
    {                                                    // Não se esqueça de criar o relacionamento nos 02 models
        return $this->hasMany(
            Season::class,
            'series_id',                      // 'series_id' é o nome da chave estrangeira lá na outra tabela (chave chega lá e portanto está lá)
            'id'                                // o campo de referência para associação. Obs: o default é o 'id', foi colocado aqui apenas para conhecimento     
        );
    }

    static protected function booted()
    {
        self::addGlobalScope('ordered', function (Builder $queryBuilder) {

            $queryBuilder->orderBy('nome');                                     // as queries já virão por padrão ordenadas
        });
    }

    /*
    public function scopeActive(Builder $queryBuilder)      //Exemplo de escopo local
    {
        return $queryBuilder->where('active', true);        // Serie::active()->get();
    }
    */

    public function links(): Attribute              // HATEOAS: Laravel, quando você for SERIALIZAR este JSON, adiciona este ATRIBUTO também como um EXTRA na resposta
    {
        return new Attribute(fn () => [             // precisa-se informar um array, uma vez que este é serializável

            [
                'rel' => 'seasons',
                'url' => "/api/series/{$this->id}/seasons"
            ],

            [
                'rel' => 'episodes',
                'url' => "/api/series/{$this->id}/episodes"
            ]
        ]);
    }
}

// Quando você for serializar este JSON, adiciona este ATRIBUTO também

/*

# Diferentemente do Doctrine, em que temos a separação entre model e repository, no Eloquent, o model faz tudo.

# O modelo se baseia AUTOMATICAMENTE na tabela.

# O eloquent segue o padrão de tabelas no plural minúsculo, ou seja, buscará uma tabela 'series'.
    Poderíamos especificar uma tabela com: 
        protected $table = 'seriados';

*/



/* ##### RELACIONAMENTOS (eloquent relationships) #####

One To Many: hasMany <-> belongsTo

One To One

Many To Many (precisa de uma tabela intermediária)

*/



/* ##### HATEOAS - Hypermidia As The Engine Of the Application State #####

nós ajudamos o cliente com informações sobre a navegação. Seja entre páginas ou entre recursos.
Ou seja, enviamos informações além da mídia solicitada.

Dados que não são somente texto, ou seja, imagens, vídeos ou como no nosso caso, links, são comumente tratados 
como hypermidia, ou hipermídia.

A utilização de hipermídia como o motor de uma API RESTful é muito comum e amplamente utilizada. 
Para este tipo de técnica, se deu o nome de Hypermidia As The Engine Of the Application State, ou, HATEOAS.

Este componente de uma API é o que diferencia o padrão REST de qualquer outro padrão de arquitetura. 
Fornecer informações para que o cliente consiga navegar entre os nossos recursos é extremamente útil e 
pode facilitar (e muito) a utilização da nossa solução.

*/
