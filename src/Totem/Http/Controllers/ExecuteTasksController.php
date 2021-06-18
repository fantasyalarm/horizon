<?php

namespace Laravel\Horizon\Totem\Http\Controllers;

use Laravel\Horizon\Totem\Contracts\TaskInterface;
use Laravel\Horizon\Totem\Task;

class ExecuteTasksController extends Controller
{
    /**
     * @var TaskInterface
     */
    private $tasks;

    /**
     * @param TaskInterface $tasks
     */
    public function __construct(TaskInterface $tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * Execute a specific task.
     *
     * @param $task
     * @return \Illuminate\Http\Response
     */
    public function index($task)
    {
        $this->tasks->execute($task);

        return Task::find($task->id);
    }
}
