<?php

namespace App\Http\Controllers\Web;

use App\Events\SeriesCreated as SeriesCreatedEvent;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Autenticador;
use App\Http\Requests\SeriesFormRequest;
use App\Jobs\DeleteSeriesCoverFile;
use App\Jobs\LogSerieDeleted;
use App\Mail\SeriesCreated;
use App\Models\Serie;
use App\Models\User;
use App\Repositories\SerieRepository;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SeriesController extends Controller
{
    /* ### Outra maneira de usar Injeção de Dependência. (PHP 8+)

    public function __construct(SerieRepository $repository)
    {        
    }
    */

    public function __construct()
    {
        $this->middleware(Autenticador::class)      // A vantagem de se usar o middleware diretamente no controller
            ->except('index');                      // é a possibilidade de adicionar um exceção
    }

    public function index(Request $request)
    {
        // return $request->get('id');             // Query Params. Podemos utilizar o método 'query' que vai gerar exatamente o mesmo resultado. A diferença entre o método get e o método query é que o método get busca o dado de qualquer lugar do nosso request, seja da query string ou mesmo de um campo enviado por post. Por isso o ideal é utilizar o método query para que nosso código fique mais explícito, deixando claro de onde vamos buscar o dado
        // return $request->url();                 // url
        // return $request->method();              // método usado para acessar a requisição
        // return $request->input();               // Formulário

        // return response('', 302, ['Location' => 'https://google.com']);      // São
        // return redirect('https://google.com');                               // equivalentes

        /*
        $series = [

            'Prison Break',
            'Supernatural',
            'Dexter',
            'Lost',
            'Breaking Bad'
        ];
        */

        // $series = DB::select("SELECT nome FROM series");
        $series = Serie::all();                                       // Como no Doctrine, isto não é um Array e sim uma Collection
        // $series = Serie::query()->paginate(3);                    // paginação                          

        // $series = Serie::query()                                  // query builder
        //     ->orderBy('nome')
        //     ->get();

        // $series = Serie::with(['seasons'])->get();           // já traz nossos relacionamentos. Por opção adicionamos na model. Usaremos 'Serie::all()'

        // $mensagemSucesso = $request->session()->get('mensagemSucesso');           // Poderia checar com 'session()->has()', mas não há necessidade. Se não houvesse o retorno seria um null.
        // $request->session()->forget('mensagemSucesso');

        $mensagemSucesso = $request->session()->get('mensagem.sucesso');            // O 'flash()' já avisa para o Laravel apagar a mensagem.

        /*
        $series = Serie::where('nome', 1)                      // since each Eloquent model serves as a query builder,
            ->orderBy('name')                                  // you may add additional constraints to queries and
            ->take(10)                                         // then invoke the 'get' method to retrieve the results.
            ->get();
        */

        // dd($series);                                        // Função do Laravel, 'dump and die'. Debuga e para.

        /*
        return view(                                     // Já fizemos algo assim no MVC.
            'listar-series',
            [
                'series' => $series
            ]
        );
        */
        // return view('listar-series', compact('series'));     // oposto da função 'extract'. Criar array a partir da variável

        return view('series.index')         // 1º Por conveção, o arquivo html tem o mesmo nome do método correspondente do controller.
            ->with('series', $series)       // 2º Organizamos por controller e indicamos a separação não por '/' e sim por '.'
            ->with('mensagemSucesso', $mensagemSucesso);
    }

    public function create(Request $request)
    {
        return view('series.create');

        /* Cross-Site Request Forgery (CSRF) (Erro 419 PAGE EXPIRED)

            O Laravel possui uma proteção contra um ataque chamado Cross-Site Request Forgery (CSRF).
            Todo formulário que nós enviamos para o Laravel precisa ter uma informação extra: um token.
            Esse token permite que o Laravel verifique que a requisição realmente foi enviada por um formulário
            do site.
            Felizmente essa informação é simples de se adicionar, bastando usar a diretiva @csrf do blade.
        */
    }

    /* 
    public function store(Request $request)
    {
        // $nomeSerie = $request->input('nome');
        // $nomeSerie = $request->nome;                         // outra forma de se pegar um input

        /* ### Retrieving A Portion Of The Input Data ###

        $input = $request->only(['username', 'password']);
        $input = $request->only('username', 'password');

        $input = $request->except(['credit_card']);
        $input = $request->except('credit_card');

        */

    /* ### Facades ###
        $resp = DB::insert(                                     // retorna um boolean

            "INSERT INTO series(nome) VALUES(?)",
            [$nomeSerie]
        );

        return $resp ? 'OK' : 'Erro';
        */

    /*
        $serie = new Serie();
        $serie->nome = $nomeSerie;
        $serie->save();
        */

    /*      $request->validate([                    // O usuário será redirecionado e as informações de erro serão adicionadas à requisição de forma
            'nome' => ['required', 'min:3']      // muito semelhante ao que vimos com as flash messages. Se ao invés de uma aplicação full stack
        ]);                                     // nós estivermos criando uma API, ao invés de devolver uma resposta de redirecionamento, o Laravel vai responder utilizando um JSON com os erros de validação.

        ##### TRANSACTION #####
        $serie = DB::transaction(function() use ($request){              // Caso não houvesse retorno da função, eu teria que usar a variável $serie por referência: $serie = null; DB::transaction(function() use ($request, &$serie) {

            $serie = Serie::create($request->all());                 // mass assignment

            for ($s=0; $s <= $request->seasonsQty ; $s++) { 
            
                $season = $serie->seasons()->create([               // note o relacionamento
    
                    'numero' => $s
                ]);
    
                for ($e=0; $e <$request->episodesPerSeason ; $e++) { 
                    
                    $season->episodes()->create([                   // note o relacionamento
    
                        'numero' => $e
                    ]);
                }
            }

            return $serie;
        });

        /*##### Mais sobre TRANSACTIONS #####
            // Também posso usar a sintaxe 'DB::beginTransaction()' ao invés de 'DB::transaction()'.
            // Em caso de try/catch, ficaria mais elegante.

        try {

            DB::beginTransaction();

            // (code...)

            DB::commit();
        } catch (\Throwable $th) {

            DB::rollBack();
        }
        */

    /*      $request->session()->flash('mensagem.sucesso', "Série '{$serie->nome}' adicionada com sucesso");

        // return redirect('/series');
        // return redirect(route('series.index'));
        return redirect()->route('series.index');       // POST - Redirect - GET -> após uma requisição Post SEMPRE redirecione o usuário para uma requisição Get
    }
    */

    // public function store(SeriesFormRequest $request)        // Ver __construct() acima
    public function store(SeriesFormRequest $request, SerieRepository $repository)
    {   // Usaremos um REPOSITORY e também INJEÇÃO DE DEPENDÊNCIA. Service Container

        ##### UPLOAD #####
        // dd($request->file('cover'));                         // para pegar um campo, utilizamos 'input'. Arquivos utizamos file('nome do input')

        if ($request->file('cover')) {

            $filePath = $request->file('cover')                     // 'storeAs' pode adicionar um nome no arquivo, do contrário, o Laravel o nomeará
                ->store('series_cover', 'public');                  // 'series_cover' é a folder    

            // Também pode-se usar: 
            // $path = Storage::put('images', $request->file('image'));
            // $path = Storage::putFileAs('images', $request->file('image'), $name);
            // $path = $request->file('image')->move('images', $name);

            $request->filePath = $filePath;                     // gambiarra: a request não tem o filePath, necessário para o repository
        }

        // $serie = $this->repository->add($request);           // Ver __construct() acima
        $serie = $repository->add($request);

        /*  ### Movido para listener. A responsabilidade deste método é fazer STORE, não enviar email

        $userList = User::all();

        foreach ($userList as $index => $user) {

            $email = new SeriesCreated(
                $serie->nome,
                $serie->id,
                $request->seasonsQty,
                $request->episodesPerSeason
            );
            // $email->subject = 'Usaremos o construtor do objeto';

            // Mail::to($request->user())->send($email);        // note '$request->user()'
            // Mail::to($user)->send($email);
            // sleep(2);                                        // O mailtrap, na versão gratuita, tem uma limitação de 1 email a cada 2s

            // Mail::to($user)->queue($email);                     // como 'queue' adicionamos este processo a fila de processamento (ver abaixo)

            // $scheduleTime = new \DateTime();
            // $scheduleTime->modify($index * 2 . ' seconds');
            $scheduleTime = now()->addSeconds($index * 5);          // opção do Laravel

            Mail::to($user)->later($scheduleTime, $email);       // igual ao 'queue' mas possui a opção de AGENDAR
        }
        */

        // SeriesCreatedEvent::dispatch(            // Método 1 para disparar eventos

        //     $serie->nome,
        //     $serie->id,
        //     $request->seasonsQty,
        //     $request->episodesPerSeason
        // );

        $seriesCreatedEvent = new SeriesCreatedEvent(   // Método 2 para disparar eventos

            $serie->nome,
            $serie->id,
            $request->seasonsQty,
            $request->episodesPerSeason
        );
        event($seriesCreatedEvent);

        return redirect()->route('series.index')
            ->with('mensagem.sucesso', "Série '{$serie->nome}' adicionada com sucesso");
    }

    public function destroy(Request $request, Serie $serie)     // Olha que interessante: podemos passar outro parametro para a função, e, neste caso, o nome DEVE ser exatamente igual ao do nosso request parameter
    {
        // dd($request->route());                      // para ver todos os parametros da rota
        // dd($serie);                                 // INJEÇÃO DE DEPENDÊNCIA: por baixo dos panos o Laravel faz um 'SELECT'.

        $reqParam = $request->serie;                    // id da série
        // dd($reqParam);

        // Serie::destroy($reqParam);
        $serie->delete();

        LogSerieDeleted::dispatch($serie->nome);                    // teste de job

        if ($serie->cover) {
            DeleteSeriesCoverFile::dispatch($serie->cover);         // Job para deletar o arquivo da capa
        }

        ##### Flash Message - (pequenas) informações na sessão que duram apenas 01 requisição

        // $request->session()->put('mensagemSucesso', 'Série removida com sucesso');
        // $request->session()->flash('mensagem.sucesso', 'Série removida com sucesso');

        return redirect()->route('series.index')
            ->with('mensagem.sucesso', "Série '{$serie->nome}' removida com sucesso");  // Posso passar uma flash message junto com o redirecionamento
    }

    public function edit(Serie $serie)
    {
        // dd($serie->temporadas);                          // se acessarmos como PROPRIEDADE, nós temos a COLEÇÃO.
        // dd($serie->temporadas()->whereId(1)->get());     // se acessarmos como MÉTODO, nós temos o QUERY BUILDER.

        return view('series.edit')->with('serie', $serie);
    }

    public function update(Serie $serie, SeriesFormRequest $request)
    {
        // $serie->nome = $request->nome;
        // $serie->save();

        ##### Ao invés de fazermos a validação por aqui, vamos criar a nossa própria classe de request #####

        $serie
            ->fill($request->all())     // mass assignment
            ->save();

        return redirect()->route('series.index')
            ->with('mensagem.sucesso', "Série '{$serie->nome}' atualizada com sucesso");
    }

    public function downloadCover(Serie $serie, Request $request)
    {
        if (Storage::disk('public')->exists($serie->cover)) {

            /* ### Médoto 01

            $filePath = Storage::disk('public')->path($serie->cover);

            $fileContent = file_get_contents($filePath);        // Storage::get('file.jpg');

            return response($fileContent)
                ->withHeaders([
                    'Content-Type'=> mime_content_type($filePath)
                ]);
            */


            ### Método 02

            $filePath = __DIR__ . './../../../public/storage/' . $serie->cover;

            return response()->download($filePath);
            // return response()->download($myFile, $newName, $headers);
            // return response()->download($myFile)->deleteFileAfterSend(true);
        }

        return redirect()->route('series.index');
    }
}





/* ##### CONVENÇÃO DA NOMENCLATURA DOS MÉTODOS #####

Actions Handled By Resource Controller - https://laravel.com/docs/9.x/controllers#resource-controllers

Verb	    URI	                    Action	        Route Name          Comment

GET	        /series	                index	        series.index        Display all resources
GET	        /series/create	        create	        series.create       Show the form for creating a new resource.
POST	    /series	                store	        series.store        Store a newly created resource in storage.
GET	        /series/{serie}	        show	        series.show         Display the specified resource
GET	        /series/{serie}/edit	edit	        series.edit         Show the form for editing the specified resource.
PUT/PATCH	/series/{serie}	        update	        series.update       Update the specified resource in storage
DELETE	    /series/{serie}	        destroy	        series.destroy      Remove the specified resource from storage.

*/



/* BLADE (template engine)

    <ul>
        @foreach($series as $serie)         // @ diretivas do blade

        <li> {{ $serie; }} </li>            // '{{ }}' equivale a um 'short echo'

        @endforeach
    </ul>

    ##### Components #####

    1) Os componentes DEVEM ficar dentro da pasta views/components

    2) o $slot é 'envolvido' pela tag <x-nomeDoComponente></x-nomeDoComponente>

    3) as variáveis são passadas pelo componente.

    4) @{{ nome }}          // exibe literalmente {{ nome }}. Alguns frameworks de frontend usam essa nomenclatura

    5) Não é comum passar variáveis php para javascript, mas se quiser use:

        <script>
            const series = {{ Js:from($series) }};
        </script>


    Para criar um componente baseado em classe use o comando 'php artisan make:component Layout'.

    Se quisermos criar um componente anônimo, como nosso layout, poderíamos executar
    'php artisan make:component layout –view'
    Como não há classe, podemos deixar o nome em minúsculo mesmo.

    Caso a gente queira separar nosso componentes em pastas, como forms/input.blade.php, por exemplo, podemos sem problemas.
    Na hora de utilizá-lo, vamos referenciá-lo como <x-forms.input>, usando o . como separador. Assim podemos manter nossos componentes organizados.

*/



/* FRONTEND (Laravel mix e Bootstrap)

Para instalar o Laravel mix, execute
    npm install laravel-mix --save-dev

Bootstrap
    npm install bootstrap

obs:
    Após o lançamento deste treinamento, ainda na versão 9 no Laravel, o Mix foi substituído por uma ferramenta
    chamada Vite. O propósito é muito parecido então vale a pena pesquisar sobre a migração.
    O Mix continua sendo uma ferramenta válida e atual.
    https://github.com/laravel/vite-plugin/blob/main/UPGRADE.md#migrating-from-laravel-mix-to-vite

1)
    Depois crie na raiz (junto ao composer.json) do projeto o arquivo webpack.mix.js com o seguinte conteúdo:
        const mix = require('laravel-mix');

    Agora nesse arquivo você pode definir todas as configurações. Para executar corretamente o comando
    'npm run dev (ou development)' e o mix ser executado, altere a linha "dev": "vite", para "dev": "mix",.

    Depois de rodar 'npm run development' será criada em 'public/css' o arquivo 'app.css' com todo o bootstrap

2)
    Em 'resources\css\' crie (ou altere) o arquivo app.scss.

    No arquivo layout adicionamos:
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        a função '{{ asset() }}' direciona para a pasta onde os arquivos css estão, no nosso caso a 'public'

*/



/* DATABASE

##### CONFIGURAÇÕES #####

1) Em 'config/database.php' defina o banco de dados a ser utilizado:

    'default' => env('DB_CONNECTION', 'mysql')          // a função env() retorna o valor informado no arquivo .env
                                                        // ela procura primeiro o 'DB_CONNECTION', se não encontrar, o default é o 'mysql'


2) '.env'

    DB_CONNECTION=sqlite

    DB_DATABASE='nome-completo-do-arquivo'          // ou então apenas apague. O default é a função 'database_path('database.sqlite')', a qual retorna o banco de dados 'database.sqlite' no diretório 'database'


3) 'database/'

    Vamos criar o arquivo 'database.sqlite'



##### MIGRATIONS #####

# php artisan make:migration create_series_table                        // criar tabela
# php artisan make:migration --table=episodes add_watched_episode       //tabela e nome da migration
# php artisan make:migration --table=series add_cover_column            // alterar tabela

# php artisan migrate
# php artisan migrate:fresh                                // recria tudo do zero
# php artisan migrate:rollback                              // para desfazer a última migration
# php artisan migrate:rollback --step=5                     // para desfazer várias migrations

##### MODELS #####

//  app/models

# php artisan make:model Serie

# php artisan make:model Season -m          // cria o model E a migration
# php artisan make:model Episode -m

*/



/* ##### ELOQUENT #####

##### Query Builder - https://laravel.com/docs/9.x/queries

    OBS: 'DB::table('users')' equivale a 'Users'



### SELECT STATEMENTS

$users = DB::table('users')
            ->select('name', 'email as user_email')     // you can specify what columns you want
            ->get();

$users = DB::table('users')->distinct()->get();         // distinct

$user = DB::table('users')->find(3);                        // To retrieve a single row by its ID column value


$titles = DB::table('users')->pluck('title');           // retrieve a COLLECTION of user titles
$titles = DB::table('users')->pluck('title', 'titulo');   // You may specify an Array Key



### WHERE 

$users = DB::table('users')
                ->where('votes', '=', 100)                      // you may omit '='
                ->where('age', '>', 35)
                ->where('country', '<>', 'Texas')
                ->where('name', 'like', 'T%')
                ->get();

$user = DB::table('users')->where('name', 'John')->first();     // retrieve a single row

$email = DB::table('users')->where('name', 'John')->value('email');     // extract a single value from a record

$users = DB::table('users')
            ->where('votes', '>', 100)
            ->orWhere('name', 'John')
            ->get();

$users = DB::table('users')
            ->where('votes', '>', 100)
            ->orWhere(function($query) {            //  group an "or"

                $query->where('name', 'Abigail')
                      ->where('votes', '>', 50);
            })
            ->get();


$users = DB::table('users')
           ->whereBetween('votes', [1, 100])       // whereNotBetween
           ->get();


### RAW 

$users = DB::table('users')
            ->select(DB::raw('count(*) as user_count, status'))
            ->where('status', '<>', 1)
            ->groupBy('status')
            ->get();

$orders = DB::table('orders')
            ->selectRaw('price * ? as price_with_tax', [1.0825])    // The selectRaw method can be used in place of addSelect(DB::raw()) 

$orders = DB::table('orders')
                ->whereRaw('price > IF(state = "TX", ?, 100)', [200])
                ->get();

$orders = DB::table('orders')
                ->select('department', DB::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();            

$orders = DB::table('orders')
                ->orderByRaw('updated_at - created_at DESC')
                ->get();


### AGGREGATES

price = DB::table('orders')->max('price');      // count, max, min, avg, and sum



### JOINS

$users = DB::table('users')
            ->join('contacts', 'users.id', '=', 'contacts.user_id')     // leftJoin, rightJoin
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->select('users.*', 'contacts.phone', 'orders.price')
            ->get();

    // The first argument passed to the join method is the name of the table you need to join to, 
    // while the remaining arguments specify the column constraints for the join. 
    // You may even join multiple tables in a single query



### UNIONS

$first = DB::table('users')
            ->whereNull('first_name');
 

$users = DB::table('users')
            ->whereNull('last_name')        // unionAll
            ->union($first)             
            ->get();


### CHUNKING (thousands of database records)

DB::table('users')->orderBy('id')->chunk(100, function ($users) {       // chunks of 100 records at a time

    foreach ($users as $user) {
        //(...)
    }
});



DB::table('users')->where('active', false)
    ->chunkById(100, function ($users) {

        foreach ($users as $user) {

            DB::table('users')
                ->where('id', $user->id)
                ->update(['active' => true]);
        }
    });

*/

/* ##### MIDDLEWARE #####

php artisan make:middleware Autenticador

*/



/* ##### BREEZE #####

1) composer require laravel/breeze --dev

2) npm install

3) npm run dev                              // precisa do resources\js\app.js -> require('./bootstrap')
                                            // resources\js\bootstrap.js
4) php artisan breeze:install

// cuidado, ele sobrescreve as rotas

*/



/* ##### EMAIL #####

# php artisan make:mail SeriesCreated             // classe Email Criado, será criada em : app\Mail

# vamos criar um template html para o email em: resources\views\mail

##### Configuração do servidor smtp #####

config\mail.php; ou

# .env                  // usaremos o mailtrap para teste. Qualquer email enviado, não importa para qual endereço, cairá no mailtrap

    MAIL_MAILER=smtp
    MAIL_HOST=smtp.mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=120a9143955592
    MAIL_PASSWORD=87f41a3eafa17c
    MAIL_ENCRYPTION=tls

    MAIL_FROM_ADDRESS=null
    MAIL_FROM_NAME="${APP_NAME}"        // por padrão o APP_NAME é o Laravel (ver lá em cima)

*/

/* ##### Removendo o logo do Laravel do Email #####

If you want to change only the branding then you can set it in .env file
    APP_NAME=your_app_name

But if you want to change more stuff, for example the header or footer then you need to do this:

Run these commands

    php artisan vendor:publish --tag=laravel-notifications
    php artisan vendor:publish --tag=laravel-mail

and then in

    /resources/views/vendor/mail/html/

you can edit all the components and customize anything you want. 
For example i have edited the sentence All rights reserved. to All test reserved in

    /resources/views/vendor/mail/html/message.blade.php

*/



/* ##### FILAS DE PROCESSAMENTO / EVENTOS ASSÍNCRONOS #####

# Em config\queue.php:

    'default' =>env('QUEUE_CONNECTION', 'sync');    // default é sync

        'QUEUE_CONNECTION' pode ser:

            'sync'  -> 
            'database'  -> controle da fila feito por uma tabela no banco de dados tradicional
            'beanstalkd'  -> 
            'sqs'  -> amazon
            'redis'  -> banco de dados chave/valor bem rápido
            'null'  -> 

# Usaremos 'database'. Em '.env' :

    QUEUE_CONNECTION=database

# Precisamos agora da tabela de fila de processamento (JOBS):

    php artisan queue:table         // cria a migration de jobs
    php artisan migrate

# Dica: 

    php artisan tinker      // abre um terminal interativo (algo como php -a) com o Laravel e nossa aplicação inicializada

    DB::select("SELECT * FROM jobs");
    
    DB::select("SELECT * FROM failed_jobs");

    quit

# PROCESSANDO A FILA

// !: Em outro terminal:

php artisan queue:work                                       // Fica "escutando" a fila de processamento

php artisan queue:work --tries=2 --delay=10                  // parametros

    // queue:listen VS queue:work

    // Quando executamos o comando 'php artisan queue:work' nossa aplicação Laravel é carregada em memória e 
    // fica rodando “para sempre” esperando novos processos serem adicionados à fila. Sempre que alteramos o 
    // código devemos interromper o comando e rodá-lo de novo, ou executar 
    // 'php artisan queue:restart' para reiniciar o worker. 

    // Já o php 'artisan queue:listen' recarrega a aplicação a cada processo da fila que for processar, 
    // o que é menos eficiente. Normalmente usamos listen em desenvolvimento e work em produção.



    // ### Supervisor Configuration

    // In production, you need a way to keep your queue:work processes running.

    // For this reason, you need to configure a process monitor that can detect when your queue:work processes exit and 
    // automatically restart them. 

    // Supervisor is a process monitor commonly used in Linux environments and we will discuss how to configure it in 
    // the following documentation.

    // # Installing Supervisor

    //     sudo apt-get install supervisor

    // # Configuring Supervisor

    // Supervisor configuration files are typically stored in the /etc/supervisor/conf.d directory. 
    // Within this directory, you may create any number of configuration files that instruct supervisor how your 
    // processes should be monitored. For example, let's create a laravel-worker.conf file that starts and monitors 
    // queue:work processes:

    //     [program:laravel-worker]
    //     process_name=%(program_name)s_%(process_num)02d
    //     command=php /home/forge/app.com/artisan queue:work sqs --sleep=3 --tries=3 --max-time=3600
    //     autostart=true
    //     autorestart=true
    //     stopasgroup=true
    //     killasgroup=true
    //     user=forge
    //     numprocs=8
    //     redirect_stderr=true
    //     stdout_logfile=/home/forge/app.com/worker.log
    //     stopwaitsecs=3600

    // In this example, the numprocs directive will instruct Supervisor to run eight queue:work processes 
    // and monitor all of them, automatically restarting them if they fail. 
    // You should change the command directive of the configuration to reflect your desired queue connection 
    // and worker options.

    // You should ensure that the value of stopwaitsecs is greater than the number of seconds consumed by your 
    // longest running job. Otherwise, Supervisor may kill the job before it is finished processing.

    // Starting Supervisor

    // Once the configuration file has been created, you may update the Supervisor configuration and start the 
    // processes using the following commands:

    // sudo supervisorctl reread
        
    //     sudo supervisorctl update
        
    //     sudo supervisorctl start laravel-worker:*

php artisan queue:failed                // lista os jobs que falharam

php artisan queue:retry "all"           // 'queue:retry' tem como parametro o id do job. Podemos tbm usar o "all"
                                        // ele em si não processa, apenas volta para a fila, onde o 'queue:work' já está trabalhando

php artisan queue:clear                 // limpa tabela jobs

*/



/* ##### EVENT and EVENT LISTENER #####

php artisan make:listener EmailUsersAboutSeriesCreated              // cria a classe Listener em 'app\Listeners'

php artisan make:event SeriesCreated                                // cria o EVENT en 'app\Events'

php artisan make:listener LogSeriesCreated -e SeriesCreated         // crio o listener já ouvindo um event

### Associando O 'event' ao seu 'event listener'

Em 'app\Providers\EventServiceProvider.php' adionar ao $listen

OBS!!!: 
    Eventos por default são SINCRONOS. Para torná-los ASSINCRONOS, na classe LISTENER (quem executa) adicione
        'implements ShouldQueue'

*/



/* ##### JOBS #####

Neste capítulo nós criamos events e event listeners para lidar com processamento assíncrono.

Mas em alguns casos nós podemos querer algo mais simples. Simplesmente iniciar uma tarefa informando que ela 
deve ser processada de forma assíncrona, sem precisar de tantas configurações.

Nestes cenários nós podemos utilizar os Jobs do Laravel. 

# Normalmente nós utilizamos Jobs para tarefas que são inerentes à regra de negócio que estamos executando. 

# Já eventos usamos para realizar tarefas “extra” (como notificar através de e-mails, realizar logs, etc.).

Utilizar Jobs é um pouco mais simples do que eventos. Basta ter uma classe que representa a tarefa a ser executada 
que pode ser criada através do comando: 
    'php artisan make:job NomeDaClasseDoJob'. 

Nela você vai ter um método 'handle' que executa a tarefa. 

Repare que esta classe também pode implementar a 'interface ShouldQueue', ou seja, esse Job pode ser processado 
de forma assíncrona.

Para enviar o job para fila, basta executar:
    'NomeDaClasseDoJob::dispatch()' 
passando por parâmetro quaisquer valores que o construtor precise. 

Dessa forma não precisamos de uma classe específica para o evento e outra para o listener.

Nesse link você pode ler todos os detalhes sobre filas e jobs: https://laravel.com/docs/9.x/queues.

Também há detalhes sobre eventos que nós não tratamos neste treinamento como Event Subscribers, 
que basicamente são listeners de vários eventos diferentes. 
Para conhecer todos os detalhes sobre eventos você pode consultar esse link da documentação: https://laravel.com/docs/9.x/events.

Ex:

php artisan make:job LogSerieDeleted            // app\Jobs

    obs1: jobs por default parece sem assincronos (confirmar).

    obs1: não esqueça o CONTRUCT.

*/

/* ##### UPLOAD #####

se necessário remova ';' do ';extension=php_fileinfo' em 'php.ini'

// Vamos adicionar a imagem (caminho dela) no banco de dados

php artisan make:migration --table=series add_cover_column


// Para configurar onde o Laravel fará o storage vá em:

'config\filesystems.php'

local salva em: 'storage\app'
public salva em: 'storage\app\public'

### Linkando os arquivos

// os arquivos armazenados em 'storage\app\public' não são públicos, para torná-los assim, é preciso criar
// um link com a pasta 'public':
    php artisan storage:link

// Agora é possível acessar a imagem diretamente:
    http://localhost:8000/storage/series_cover/fcwGJSgVdTbyKozCOya5PUwJ2JXlwHwTKdytICF8.png

*/