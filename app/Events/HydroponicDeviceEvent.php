<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HydroponicDeviceEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $id;
    public object $pumps;
    public object $sensors;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(int $id, object $pumps, object $sensors)
    {
        $this->id = $id;
        $this->pumps = $pumps;
        $this->sensors = $sensors;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('hydroponic.device.' . $this->id);
    }
}
