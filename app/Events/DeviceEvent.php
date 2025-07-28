<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeviceEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $perangkat_id;
    public $selenoid;
    public $motor1;
    public $motor2;
    public $air;
    public $pemupukan;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($perangkat_id, $selenoid, $motor1, $motor2, $air = 0, $pemupukan = 0)
    {
        $this->perangkat_id = $perangkat_id;
        $this->selenoid = $selenoid;
        $this->motor1 = $motor1;
        $this->motor2 = $motor2;
        $this->air = $air;
        $this->pemupukan = $pemupukan;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('device-status');
    }
}
