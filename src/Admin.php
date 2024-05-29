<?php

namespace DigitalNode\AcornQueueMonitor;

use DigitalNode\AcornQueueMonitor\Services\JobService;

class Admin
{
    public function __construct(
        protected PendingJobsTable $pendingJobsTable,
        protected FailedJobsTable $failedJobsTable
    ) {
    }

    public function queues_menu_page()
    {
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

    public function queues_pending_jobs_menu_subpage()
    {
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

    public function queues_failed_jobs_menu_subpage()
    {
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

    public function handle_aqm_retry_job()
    {
        if (isset($_GET['action']) && $_GET['action'] === 'aqm_retry_job' && isset($_GET['post'])) {
            if (! check_admin_referer('aqm_retry_job')) {
                wp_die(__('Nice try.', 'acorn-queue-monitor'));
            }

            $job_uuid = sanitize_text_field($_GET['uuid']);

            $result = app(JobService::class)->retryJob($job_uuid);

            if (! is_wp_error($result)) {
                $_SESSION['aqm-retry-job-status'] = 'success';

                wp_redirect(wp_get_referer());
                exit;
            } else {
                $_SESSION['aqm-retry-job-status'] = 'error';

                wp_redirect(wp_get_referer());
                exit;
            }
        }
    }

    public function display_admin_notices()
    {
        if (isset($_SESSION['aqm-retry-job-status']) && $_SESSION['aqm-retry-job-status'] == 'success') {
            unset($_SESSION['aqm-retry-job-status']);
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e('The job was retried successfully', 'acorn-queue-monitor'); ?></p>
            </div>
            <?php
        } elseif ((isset($_SESSION['aqm-retry-job-status']) && $_SESSION['aqm-retry-job-status'] == 'success')) {
            unset($_SESSION['aqm-retry-job-status']);
            ?>
            <div class="notice notice-error is-dismissible">
                <p><?php _e('The job was not retried. Please try again.', 'acorn-queue-monitor'); ?></p>
            </div>
            <?php
        }
    }
}
