<?php

namespace DigitalNode\AcornQueueMonitor\Providers;

use DigitalNode\AcornQueueMonitor\Admin;
use DigitalNode\AcornQueueMonitor\Contracts\JobRepository;
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
        $this->app->bind('Table', fn() => new Table());

        $this->app->bind('Admin', fn() => new Admin(
            $this->app->make('Table')
        ));

        $this->app->singleton('AcornQueueMonitor', function () {
            return new AcornQueueMonitor($this->app);
        });

        // TODO: Allow multiple job drivers in future.
        $this->app->bind(JobRepository::class, DatabaseRepository::class);

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
            __DIR__.'/../../config/acorn-queue-monitor.php' => $this->app->configPath('acorn-queue-monitor.php'),
        ], 'config');

        add_action('init', function(){
            $this->app->make('AcornQueueMonitor');
        });
    }
}
