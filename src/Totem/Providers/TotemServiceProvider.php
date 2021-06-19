<?php

namespace Laravel\Horizon\Totem\Providers;

use Cron\CronExpression;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Laravel\Horizon\Totem\Console\Commands\ListSchedule;
use Laravel\Horizon\Totem\Console\Commands\PublishAssets;
use Laravel\Horizon\Totem\Contracts\TaskInterface;
use Laravel\Horizon\Totem\Repositories\EloquentTaskRepository;

class TotemServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerResources();

        Validator::extend('cron_expression', function ($attribute, $value, $parameters, $validator) {
            return CronExpression::isValidExpression($value);
        });

        Validator::extend('json_file', function ($attribute, UploadedFile $value, $validator) {
            return $value->getClientOriginalExtension() == 'json';
        });
    }

    /**
     * Register any services.
     *
     * @return void
     */
    public function register()
    {
        if (! defined('TOTEM_PATH')) {
            define('TOTEM_PATH', realpath(__DIR__.'/../../'));
        }

        if (! defined('TOTEM_TABLE_PREFIX')) {
            define('TOTEM_TABLE_PREFIX', config('totem.table_prefix'));
        }

        if (! defined('TOTEM_DATABASE_CONNECTION')) {
            define('TOTEM_DATABASE_CONNECTION', config('totem.database_connection', Schema::getConnection()->getName()));
        }

        $this->commands([
            ListSchedule::class,
            PublishAssets::class,
        ]);

        $this->app->bindIf('totem.tasks', EloquentTaskRepository::class, true);
        $this->app->alias('totem.tasks', TaskInterface::class);
        $this->app->register(TotemRouteServiceProvider::class);
        $this->app->register(TotemEventServiceProvider::class);
        $this->app->register(TotemFormServiceProvider::class);
        $this->app->register(ConsoleServiceProvider::class);
    }

    /**
     * Register the Totem resources.
     *
     * @return void
     */
    protected function registerResources()
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'totem');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'totem');
    }
}
