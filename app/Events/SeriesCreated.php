<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SeriesCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $serieNome;
    public int $serieId;
    public int $serieSeasonsQty;
    public int $serieEpisodesPerSeason;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        string $serieNome,
        int $serieId,
        int $serieSeasonsQty,
        int $serieEpisodesPerSeason
    ) {
        $this->serieNome = $serieNome;
        $this->serieId = $serieId;
        $this->serieSeasonsQty = $serieSeasonsQty;
        $this->serieEpisodesPerSeason = $serieEpisodesPerSeason;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
