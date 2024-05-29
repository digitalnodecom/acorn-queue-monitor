<?php

namespace DigitalNode\AcornQueueMonitor\Repositories;

use DigitalNode\AcornQueueMonitor\Contracts\JobRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DatabaseRepository implements JobRepository {

  public function countFailed() {
    // TODO: Implement countFailed() method.
  }

  public function countPending() {
    // TODO: Implement countPending() method.
  }

  public function countCompleted() {
    return DB::table('jobs')->count();
  }

  public function getJobsByIds( array $ids = [] ): Collection {
    return collect([]);
  }

  public function getJobs( int $per_page = 20, int $offset = 0 ): Collection {
    return DB::table('jobs')
       ->select('*')
       ->limit($per_page)
       ->offset($offset)
       ->get();
  }
}
