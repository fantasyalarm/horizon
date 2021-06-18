<?php

namespace Laravel\Horizon\Totem\Http\Controllers;

use Laravel\Horizon\Totem\Contracts\TaskInterface;
use Laravel\Horizon\Totem\Http\Requests\ImportRequest;

class ImportTasksController extends Controller
{
    /**
     * @var TaskInterface
     */
    private $tasks;

    /**
     * ImportTasksController constructor.
     * @param TaskInterface $tasks
     */
    public function __construct(TaskInterface $tasks)
    {
        parent::__construct();

        $this->tasks = $tasks;
    }

    /**
     * Import tasks from a json file.
     * @param \Studio\Totem\Http\Requests\ImportRequest $request
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function index(ImportRequest $request)
    {
        $this->tasks->import($request->validated());
    }
}
