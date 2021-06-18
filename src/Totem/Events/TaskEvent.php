<?php

namespace Laravel\Horizon\Totem\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Laravel\Horizon\Totem\Task;

class TaskEvent extends Event
{
    use Dispatchable, SerializesModels;

    /**
     * @var Task
     */
    public $task;

    /**
     * Constructor.
     *
     * @param Task $task
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }
}
