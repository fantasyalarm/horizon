<?php

namespace Laravel\Horizon\Totem\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Laravel\Horizon\Totem\Contracts\TaskInterface;
use Laravel\Horizon\Totem\Events\Activated;
use Laravel\Horizon\Totem\Events\Created;
use Laravel\Horizon\Totem\Events\Creating;
use Laravel\Horizon\Totem\Events\Deactivated;
use Laravel\Horizon\Totem\Events\Deleted;
use Laravel\Horizon\Totem\Events\Deleting;
use Laravel\Horizon\Totem\Events\Executed;
use Laravel\Horizon\Totem\Events\Updated;
use Laravel\Horizon\Totem\Events\Updating;
use Laravel\Horizon\Totem\Result;
use Laravel\Horizon\Totem\Task;

class EloquentTaskRepository implements TaskInterface
{
    /**
     * Return task eloquent builder.
     *
     * @return Task
     */
    public function builder(): Builder
    {
        $result = new Result;

        return (new Task)->select(TOTEM_TABLE_PREFIX.'tasks.*')
            ->selectSub(
                $result->getLastRun(),
                'last_ran_at'
            )
            ->selectSub(
                $result->getAverageRunTime(),
                'average_runtime'
            );
    }

    /**
     * Find a task by id.
     *
     * @param int|Task $id
     * @return int|Task
     */
    public function find($id)
    {
        if ($id instanceof Task) {
            return $id;
        }

        return Cache::rememberForever('totem.task.'.$id, function () use ($id) {
            return Task::find($id);
        });
    }

    /**
     * Find all tasks.
     *
     * @return mixed
     */
    public function findAll()
    {
        return Cache::rememberForever('totem.tasks.all', function () {
            return Task::all();
        });
    }

    /**
     * Find all active tasks.
     *
     * @return mixed
     */
    public function findAllActive()
    {
        return Cache::rememberForever('totem.tasks.active', function () {
            return $this->findAll()->filter(function ($task) {
                return $task->is_active;
            });
        });
    }

    /**
     * Create a new task.
     *
     * @param array $input
     * @return bool|Task
     */
    public function store(array $input)
    {
        $task = new Task;

        if (Creating::dispatch($input) === false) {
            return false;
        }

        $task->fill(Arr::only($input, $task->getFillable()))->save();

        Created::dispatch($task);

        return $task;
    }

    /**
     * Update the given task.
     *
     * @param array $input
     * @param Task $task
     * @return bool|int|Task
     */
    public function update(array $input, $task)
    {
        $task = $this->find($task);

        if (Updating::dispatch($input, $task) === false) {
            return false;
        }

        $task->fill(Arr::only($input, $task->getFillable()))->save();

        Updated::dispatch($task);

        return $task;
    }

    /**
     * Delete the given task.
     *
     * @param int|Task $id
     * @return bool
     */
    public function destroy($id)
    {
        $task = $this->find($id);

        if (Deleting::dispatch($task->id) === false) {
            return false;
        }

        $task->delete();

        Deleted::dispatch();

        return true;
    }

    /**
     * Activate the given task.
     *
     * @param $input
     * @return int|Task
     */
    public function activate($input)
    {
        $task = $this->find($input['task_id']);

        $task->fill(['is_active' => true])->save();

        Activated::dispatch($task);

        return $task;
    }

    /**
     * Deactive the given task.
     *
     * @param $id
     * @return int|Task
     */
    public function deactivate($id)
    {
        $task = $this->find($id);

        $task->fill(['is_active' => false])->save();

        Deactivated::dispatch($task);

        return $task;
    }

    /**
     * Execute a given task.
     *
     * @param $id
     * @return int|Task
     */
    public function execute($id)
    {
        $task = $this->find($id);
        $start = microtime(true);
        try {
            Artisan::call($task->command, $task->compileParameters());

            Storage::put($task->getMutexName(), Artisan::output());
        } catch (\Exception $e) {
            Storage::put($task->getMutexName(), $e->getMessage());
        }

        Executed::dispatch($task, $start);

        return $task;
    }

    /**
     * Import tasks.
     *
     * @param $input
     * @return bool|int|Task|void
     */
    public function import($input)
    {
        Cache::forget('totem.tasks.all');
        Cache::forget('totem.tasks.active');

        collect(json_decode(Arr::get($input, 'content')))
            ->each(function ($data) {
                Cache::forget('totem.task.'.$data->id);

                $task = $this->find($data->id);

                if (is_null($task)) {
                    $this->store((array) $data);

                    return;
                }

                $this->update((array) $data, $task);
            });
    }
}
