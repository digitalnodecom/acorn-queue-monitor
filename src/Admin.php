<?php

namespace DigitalNode\AcornQueueMonitor;

use DigitalNode\AcornQueueMonitor\Contracts\JobRepository;

class Admin {
    public function __construct(
        protected PendingJobsTable $pendingJobsTable,
        protected FailedJobsTable $failedJobsTable
    ) {}

    public function queues_menu_page(){
        add_menu_page(
            'Pending Jobs',
            'Queues',
            'manage_options',
            'pending-jobs',
            null,
            'dashicons-clock',
            10
        );
    }

    public function queues_pending_jobs_menu_subpage(){
        add_submenu_page(
            'pending-jobs',
            'Pending Jobs',
            'Pending Jobs',
            'manage_options',
            'pending-jobs',
            function () {
                $this->pendingJobsTable->renderTable();
            },
        );
    }

    public function queues_failed_jobs_menu_subpage(){
        add_submenu_page(
            'pending-jobs',
            'Failed Jobs',
            'Failed Jobs',
            'manage_options',
            'failed-jobs',
            function () {
                $this->failedJobsTable->renderTable();
            },
        );
    }

}
