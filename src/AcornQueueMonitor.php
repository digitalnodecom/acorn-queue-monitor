<?php

namespace DigitalNode\AcornQueueMonitor;

use Illuminate\Support\Arr;
use Roots\Acorn\Application;
use function Crontrol\Schedule\add;

class AcornQueueMonitor
{
    /**
     * The application instance.
     *
     * @var \Roots\Acorn\Application
     */
    protected $app;

    /**
     * Create a new AcornQueueMonitor instance.
     *
     * @param  \Roots\Acorn\Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        $this->run();
    }

    private function run(){
        add_action('admin_menu', function(){
            /* @var Admin $admin */
            $admin = $this->app->make('Admin');

            $admin->queues_menu_page();
            $admin->queues_pending_jobs_menu_subpage();
            $admin->queues_failed_jobs_menu_subpage();
        });

        add_action('admin_init', function(){
            /* @var Admin $admin */
            $admin = $this->app->make('Admin');

            $admin->handle_aqm_retry_job();
        });

        add_action('admin_notices', function (){
            /* @var Admin $admin */
            $admin = $this->app->make('Admin');

            $admin->display_admin_notices();
        });
    }
}
