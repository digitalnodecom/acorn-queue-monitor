<?php

namespace DigitalNode\AcornQueueMonitor;

use Illuminate\Support\Arr;
use Roots\Acorn\Application;

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
        });
    }
}
