<?php

namespace App\Events;

use App\Models\Inspection;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Real-time (Pusher) event fired when an inspection is assigned/reassigned to a
 * technician — for live in-app updates while the app is open. Complements the
 * FCM push (which also reaches a closed app).
 */
class InspectionAssigned implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $technicianId,
        public int $inspectionId,
        public int $leadId,
        public string $title,
        public string $body,
        public string $type = 'inspection_assigned',
    ) {
    }

    /**
     * Broadcast on the assigned technician's private channel.
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel('technician.' . $this->technicianId)];
    }

    /**
     * Event name the app listens for.
     */
    public function broadcastAs(): string
    {
        return 'inspection.assigned';
    }

    /**
     * Payload delivered to the app.
     */
    public function broadcastWith(): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'body' => $this->body,
            'inspection_id' => $this->inspectionId,
            'lead_id' => $this->leadId,
        ];
    }
}
