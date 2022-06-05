<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class VideoCall implements ShouldBroadcastNow
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $actionId;
  public $actionData;
  public $patientid;

  /**
    * Create a new event instance.
    *
    * @author Author
    *
    * @return void
    */
  public function __construct($actionId, $actionData , $patientid)
  {
      $this->actionId = $actionId;
      $this->actionData = $actionData;
      $this->patientid = $patientid;
  }

  /**
    * Get the channels the event should broadcast on.
    *
    * @author Author
    *
    * @return Channel|array
    */
  public function broadcastOn()
  {
      return new Channel('video-call');
  }

  /**
    * Get the data to broadcast.
    *
    * @author Author
    *
    * @return array
    */
  public function broadcastWith()
  {
      return [
          'username' => $this->actionId,    
          'channel' => $this->actionData,
          'patientid' => $this->patientid
      ];
  }

}
