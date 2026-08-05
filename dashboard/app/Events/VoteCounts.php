<?php

namespace App\Events;

use App\Models\VoteCount;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class VoteCounts
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public VoteCount $votecount;

    /**
     * Create a new event instance.
     */
    public function __construct(VoteCount $votecount)
    {
        $this->votecount = $votecount;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('vc.' . $this->votecount->vote_id),
        ];
    }
    public function broadcastAs()
    {
        return 'votecount';
    }

    public function broadcastWith()
    {
        $vote = VoteCount::where(['vote_id' => $this->votecount->vote_id, 'paslon_id' => $this->votecount->paslon_id])->get();
        return [
            'paslon_id' => $this->votecount->paslon_id,
            'count' => $vote->count(),
        ];
    }
}
