<?php

namespace Laravel\Horizon\Totem\Http\Controllers;

use Laravel\Horizon\Totem\Contracts\TaskInterface;

class ExportTasksController extends Controller
{
    /**
     * @var TaskInterface
     */
    private $tasks;

    /**
     * ExportTasksController constructor.
     * @param TaskInterface $tasks
     */
    public function __construct(TaskInterface $tasks)
    {
        parent::__construct();

        $this->tasks = $tasks;
    }

    /**
     * Export all tasks to a json file.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function index()
    {
        $headers = [
            'Content-Type' => 'text/json',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()
            ->streamDownload(function () {
                echo $this->tasks->findAll()->toJson();
            }, 'tasks.json',
            $headers);
    }
}
