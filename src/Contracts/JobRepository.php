<?php

namespace DigitalNode\AcornQueueMonitor\Contracts;

use Illuminate\Support\Collection;

interface JobRepository {
    /**
     * Get the count of failed jobs.
     *
     * @return int
     */
    public function countFailed();

    /**
     * Get the count of pending jobs.
     *
     * @return int
     */
    public function countPending();

    /**
     * Get the count of completed jobs.
     *
     * @return int
     */
    public function countCompleted();

    /**
     * Retrieve all jobs stored in the database;
     *
     * @return \Illuminate\Support\Collection
     */
    public function getJobs( int $per_page = 0, int $offset = 0 ): Collection;

    /**
    * Retrieve all failed jobs stored in the database;
    *
    * @return \Illuminate\Support\Collection
    */
    public function getFailedJobs( int $per_page = 0, int $offset = 0 ): Collection;

    /**
     * Retrieve the jobs with the given IDs.
     *
     * @param  array<integer>  $ids
     * @return \Illuminate\Support\Collection
     */
    public function getJobsByIds( array $ids = [] ): Collection;
}
