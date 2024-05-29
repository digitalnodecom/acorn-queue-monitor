<?php

namespace DigitalNode\AcornQueueMonitor\Providers;

use DigitalNode\AcornQueueMonitor\Admin;
use DigitalNode\AcornQueueMonitor\Contracts\JobRepository;
use DigitalNode\AcornQueueMonitor\FailedJobsTable;
use DigitalNode\AcornQueueMonitor\PendingJobsTable;
use DigitalNode\AcornQueueMonitor\Repositories\DatabaseRepository;
use DigitalNode\AcornQueueMonitor\Table;
use Illuminate\Support\ServiceProvider;
use DigitalNode\AcornQueueMonitor\Console\AcornQueueMonitorCommand;
use DigitalNode\AcornQueueMonitor\AcornQueueMonitor;

class AcornQueueMonitorServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app()->bind('PendingJobsTable', fn() => new PendingJobsTable());
        app()->bind('FailedJobsTable', fn() => new FailedJobsTable());

        app()->bind('Admin', fn() => new Admin(
            app()->make('PendingJobsTable'),
            app()->make('FailedJobsTable')
        ));

        app()->singleton('AcornQueueMonitor', function () {
            return new AcornQueueMonitor(app());
        });

        // TODO: Allow multiple job drivers in future.
        app()->bind(JobRepository::class, DatabaseRepository::class);

        $this->mergeConfigFrom(
            __DIR__.'/../../config/acorn-queue-monitor.php',
            'acorn-queue-monitor'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/acorn-queue-monitor.php' => app()->configPath('acorn-queue-monitor.php'),
        ], 'config');

        add_action('init', function(){
            if ( ! session_id() ) {
                session_start();
            }

            app()->make('AcornQueueMonitor');
        });
    }
}
