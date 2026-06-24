<?php

namespace App\Events;

use App\Model\Stream;
use App\Model\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StreamMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $stream;
    public $message;

    public function __construct(Stream $stream, ChatMessage $message)
    {
        $this->stream = $stream;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new Channel('stream.' . $this->stream->id);
    }

    public function broadcastAs()
    {
        return 'stream.message';
    }
} 