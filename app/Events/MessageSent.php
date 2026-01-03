<?php

namespace App\Events;

use App\Models\TaskMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $html;

    public function __construct(TaskMessage $message, $html)
    {
        $this->message = $message;
        $this->html = $html;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->message->task_id);
    }

    /**
     * MENAMBAHKAN ALIAS EVENT
     * Agar nama event di Pusher menjadi 'message.sent' (bukan App\Events\MessageSent)
     * Ini memudahkan frontend untuk menangkap eventnya.
     */
    public function broadcastAs()
    {
        return 'message.sent';
    }
}
