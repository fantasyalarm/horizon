<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTotemTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(config('horizon.database_connection'))
            ->create(config('horizon.table_prefix').'tasks', function (Blueprint $table) {
                $table->increments('id');
                $table->string('description');
                $table->string('command');
                $table->string('parameters')->nullable();
                $table->string('expression')->nullable();
                $table->string('timezone')->default('UTC');
                $table->boolean('is_active')->default(true);
                $table->boolean('dont_overlap')->default(false);
                $table->boolean('run_in_maintenance')->default(false);
                $table->string('notification_email_address')->nullable();
                $table->string('notification_phone_number')->nullable();
                $table->string('notification_slack_webhook')->nullable();
                $table->integer('auto_cleanup_num')->default(0);
                $table->integer('auto_cleanup_type',20)->nullable();
                $table->boolean('run_on_one_server')->default(false);
                $table->boolean('run_in_background')->default(false);
                $table->timestamps();
                $table->index('is_active', 'tasks_is_active_idx');
                $table->index('dont_overlap', 'tasks_dont_overlap_idx');
                $table->index('run_in_maintenance', 'tasks_run_in_maintenance_idx');
                $table->index('run_on_one_server', 'tasks_run_on_one_server_idx');
                $table->index('auto_cleanup_num', 'tasks_auto_cleanup_num_idx');
                $table->index('auto_cleanup_type', 'tasks_auto_cleanup_type_idx');
            });
        Schema::connection(config('horizon.database_connection'))
            ->create(config('horizon.table_prefix').'task_frequencies', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('task_id');
                $table->string('label');
                $table->string('interval');
                $table->timestamps();
                $table->index('task_id', 'task_frequencies_task_id_idx');
                $table->foreign('task_id', 'task_frequencies_task_id_fk')
                    ->references('id')
                    ->on(config('horizon.table_prefix').'tasks');
            });
        Schema::connection(config('horizon.database_connection'))
            ->create(config('horizon.table_prefix').'task_results', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('task_id');
                $table->timestamp('ran_at')->useCurrent();
                $table->decimal('duration',24,14)->default(0.0);
                $table->longText('result');
                $table->timestamps();
                $table->index('task_id', 'task_results_task_id_idx');
                $table->index('ran_at', 'task_results_ran_at_idx');
                $table->foreign('task_id', 'task_id_fk')
                    ->references('id')
                    ->on(config('horizon.table_prefix').'tasks');
            });
        Schema::connection(config('horizon.database_connection'))
            ->create(config('horizon.table_prefix').'frequency_parameters', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('frequency_id');
                $table->string('name');
                $table->string('value');
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(config('horizon.database_connection'))
            ->dropIfExists(config('horizon.table_prefix').'tasks');
        Schema::connection(config('horizon.database_connection'))
            ->dropIfExists(config('horizon.table_prefix').'task_frequencies');
        Schema::connection(config('horizon.database_connection'))
            ->dropIfExists(config('horizon.table_prefix').'task_results');
        Schema::connection(config('horizon.database_connection'))
            ->dropIfExists(config('horizon.table_prefix').'frequency_parameters');
    }
}
