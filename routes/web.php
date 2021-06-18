<?php

use Illuminate\Support\Facades\Route;

/*Route::prefix('totem')->group(function(){
    Route::get('/', [\Laravel\Horizon\Totem\Http\Controllers\DashboardController::class,'index'])->name('totem.dashboard');
    Route::group(['prefix' => 'tasks'], function () {
        Route::get('/', [\Laravel\Horizon\Totem\Http\Controllers\TasksController::class,'index'])->name('totem.tasks.all');
        Route::get('create', [\Laravel\Horizon\Totem\Http\Controllers\TasksController::class,'create'])->name('totem.task.create');
        Route::post('create', [\Laravel\Horizon\Totem\Http\Controllers\TasksController::class,'store']);

        Route::get('export', [\Laravel\Horizon\Totem\Http\Controllers\ExportTasksController::class,'index'])->name('totem.tasks.export');
        Route::post('import', [\Laravel\Horizon\Totem\Http\Controllers\ImportTasksController::class,'index'])->name('totem.tasks.import');

        Route::get('{task}', [\Laravel\Horizon\Totem\Http\Controllers\TasksController::class,'view'])->name('totem.task.view');

        Route::get('{task}/edit', [\Laravel\Horizon\Totem\Http\Controllers\TasksController::class,'edit'])->name('totem.task.edit');
        Route::post('{task}/edit', [\Laravel\Horizon\Totem\Http\Controllers\TasksController::class,'update']);

        Route::delete('{task}', [\Laravel\Horizon\Totem\Http\Controllers\TasksController::class,'destroy'])->name('totem.task.delete');

        Route::post('status', [\Laravel\Horizon\Totem\Http\Controllers\ActiveTasksController::class,'store'])->name('totem.task.activate');
        Route::delete('status/{id}', [\Laravel\Horizon\Totem\Http\Controllers\ActiveTasksController::class,'destroy'])->name('totem.task.deactivate');

        Route::get('{task}/execute', [\Laravel\Horizon\Totem\Http\Controllers\ExecuteTasksController::class,'index'])->name('totem.task.execute');
    });
});*/
Route::prefix('api')->group(function () {
    // Dashboard Routes...
    Route::get('/stats', 'DashboardStatsController@index')->name('horizon.stats.index');

    // Workload Routes...
    Route::get('/workload', 'WorkloadController@index')->name('horizon.workload.index');

    // Master Supervisor Routes...
    Route::get('/masters', 'MasterSupervisorController@index')->name('horizon.masters.index');

    // Monitoring Routes...
    Route::get('/monitoring', 'MonitoringController@index')->name('horizon.monitoring.index');
    Route::post('/monitoring', 'MonitoringController@store')->name('horizon.monitoring.store');
    Route::get('/monitoring/{tag}', 'MonitoringController@paginate')->name('horizon.monitoring-tag.paginate');
    Route::delete('/monitoring/{tag}', 'MonitoringController@destroy')->name('horizon.monitoring-tag.destroy');

    // Job Metric Routes...
    Route::get('/metrics/jobs', 'JobMetricsController@index')->name('horizon.jobs-metrics.index');
    Route::get('/metrics/jobs/{id}', 'JobMetricsController@show')->name('horizon.jobs-metrics.show');

    // Queue Metric Routes...
    Route::get('/metrics/queues', 'QueueMetricsController@index')->name('horizon.queues-metrics.index');
    Route::get('/metrics/queues/{id}', 'QueueMetricsController@show')->name('horizon.queues-metrics.show');

    // Batches Routes...
    Route::get('/batches', 'BatchesController@index')->name('horizon.jobs-batches.index');
    Route::get('/batches/{id}', 'BatchesController@show')->name('horizon.jobs-batches.show');
    Route::post('/batches/retry/{id}', 'BatchesController@retry')->name('horizon.jobs-batches.retry');

    // Job Routes...
    Route::get('/jobs/pending', 'PendingJobsController@index')->name('horizon.pending-jobs.index');
    Route::get('/jobs/completed', 'CompletedJobsController@index')->name('horizon.completed-jobs.index');
    Route::get('/jobs/failed', 'FailedJobsController@index')->name('horizon.failed-jobs.index');
    Route::get('/jobs/failed/{id}', 'FailedJobsController@show')->name('horizon.failed-jobs.show');
    Route::post('/jobs/retry/{id}', 'RetryController@store')->name('horizon.retry-jobs.show');
    Route::get('/jobs/{id}', 'JobsController@show')->name('horizon.jobs.show');
});

// Catch-all Route...
Route::get('/{view?}', 'HomeController@index')->where('view', '(.*)')->name('horizon.index');
