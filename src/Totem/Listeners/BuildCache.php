<?php

namespace Laravel\Horizon\Totem\Listeners;

use Laravel\Horizon\Totem\Events\Event;

class BuildCache extends Listener
{
    /**
     * Handle the event.
     *
     * @param  \Laravel\Horizon\Totem\Events\Event  $event
     */
    public function handle(Event $event)
    {
        $this->build($event);
    }

    /**
     * Rebuild Cache.
     *
     * @param Event $event
     */
    protected function build(Event $event)
    {
        if ($event->task) {
            $this->tasks->find($event->task->id);
        }
    }
}
