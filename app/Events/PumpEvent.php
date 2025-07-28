<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PumpEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id;
    public $irigasi;
    public $vertigasi;
    public $pe_irrigation;
    public $pe_vertigation;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($id, $irigasi, $vertigasi, $pe_irrigation, $pe_vertigation)
    {
        $this->id = $id;
        $this->irigasi = $irigasi;
        $this->vertigasi = $vertigasi;
        $this->pe_irrigation = $pe_irrigation;
        $this->pe_vertigation = $pe_vertigation;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('Pumps');
    }

    public function broadcastAs() {
        return 'PumpEvent';
    }
}
