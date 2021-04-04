<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Post;

class PostSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $user;
    public $post;

    //public $afterCommit=true;

    public function __construct(User $user,Post $post)
    {
        $this->user=$user;
        $this->post=$post;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        //dd(new PrivateChannel('room.'.$this->post->room_id));
        return new PrivateChannel('room.'.$this->post->room_id);
    }

    public function broadcastAs()
    {
        return 'room.created';
    }
}