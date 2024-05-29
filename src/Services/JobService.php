<?php

namespace DigitalNode\AcornQueueMonitor\Services;

use Illuminate\Support\Facades\Artisan;

class JobService {
    public function retryJob( string $job_uuid ): int|\WP_Error {
        $result = Artisan::call( 'queue:retry ' . $job_uuid );

        if ( $result !== 0 ){
            return new \WP_Error( __('Failed to retry the job with ID #.' . $job_uuid, 'acorn-queue-monitor') );
        }

        return $result;
    }
}
