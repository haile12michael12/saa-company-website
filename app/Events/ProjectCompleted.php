<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public mixed $project = null)
    {
    }
}