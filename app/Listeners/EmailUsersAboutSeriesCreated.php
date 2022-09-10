<?php

namespace App\Listeners;

use App\Events\SeriesCreated as SeriesCreatedEvent;     // alias
use App\Mail\SeriesCreated;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class EmailUsersAboutSeriesCreated implements ShouldQueue   // talvez não seja necessário pq o Mail já faz essa parte
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(SeriesCreatedEvent $event)       // recebe o EVENTO
    {
        $userList = User::all();

        foreach ($userList as $index => $user) {

            $email = new SeriesCreated(
                $event->serieNome,
                $event->serieId,
                $event->serieSeasonsQty,
                $event->serieEpisodesPerSeason
           
            );
            $scheduleTime = now()->addSeconds($index * 5);          

            Mail::to($user)->later($scheduleTime, $email);       
        }
    }
}
