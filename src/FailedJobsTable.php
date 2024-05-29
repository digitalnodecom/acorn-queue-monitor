<?php

namespace DigitalNode\AcornQueueMonitor;

use DigitalNode\AcornQueueMonitor\Contracts\JobRepository;
use Illuminate\Support\Carbon;

if ( ! defined( 'WPINC' ) ) die;

if (!class_exists('\WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class FailedJobsTable extends \WP_List_Table {
    CONST PER_PAGE = 20;

    public function __construct( $args = array() ) {
        parent::__construct(array(
            'singular' => 'Failed Job',
            'plural'   => 'Failed Jobs',
            'ajax'     => false
        ));
    }

    public function prepare_items() {
        /* @var JobRepository $jobRepository */
        $jobRepository = app()->make(JobRepository::class);

        $jobs = $jobRepository->getFailedJobs(
            self::PER_PAGE,
            $this->get_pagenum() - 1
        )->all();

        $this->set_pagination_args([
            'total_items' => $jobRepository->countCompleted(),
            'per_page'    => self::PER_PAGE,
        ]);

        // Sort the data
        usort($jobs, array($this, 'usort_reorder'));

        $this->_column_headers = array(
            $this->get_columns(),
            $this->get_hidden_columns(),
            $this->get_sortable_columns()
        );

        $this->items = $jobs;
    }

    private function usort_reorder($a, $b) {
        $orderby = ( !empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'id';

        $order = ( !empty( $_GET['order'] ) ) ? $_GET['order'] : 'asc';

        $result = strcmp( $a->{$orderby}, $b->{$orderby} );

        return ($order === 'asc') ? $result : -$result;
    }

    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'id':
                return $item->uuid;
            case 'payload':
                return '<code>' . $item->payload . '</code>';
            case 'exception':
                return '<div style="max-height: 200px; overflow-y: scroll;" ">' . $item->exception . '</div>';
            case 'failed_at':
                return isset($item->failed_at) ? Carbon::parse($item->failed_at)->diffForHumans() : 'n/a';
            default:
                return $item->{$column_name} ?? '';
        }
    }

    public function get_columns()
    {
        $columns = array(
            'id' => 'UUID',
            'connection' => 'Connection',
            'queue' => 'Queue',
            'payload' => 'Payload',
            'exception' => 'Exception',
            'failed_at' => 'Failed At',
        );
        return $columns;
    }

    public function get_sortable_columns() {
        return [
            'id' => [ 'id', 'desc' ],
            'reserved_at' => [ 'reserved_at', 'desc' ],
            'available_at'=> [ 'available_at', 'desc' ],
            'created_at' => [ 'created_at', 'desc' ],
        ];
    }

    public function get_hidden_columns() {
        return [];
    }

    public function renderTable() {
        $this->prepare_items();

        ob_start();
        echo '<div class="wrap"><h2>Failed Jobs</h2>';
        echo '<form method="POST">';
        $this->display();
        echo '</form>';
        echo '</div>';

        echo ob_get_clean();
    }

    public function column_id( $item ) {
        $retry_link = wp_nonce_url(
            add_query_arg(
                array(
                    'action'    => 'aqm_retry_job',
                    'post'      => $item->uuid,
                ),
                admin_url()
            ),
            'aqm_retry_job'
        );

        $output = esc_html(  $item->uuid  );

        $output .= '<div class="row-actions"><a href="' . esc_url( $retry_link ) . '">' . esc_html__( 'Retry', 'acorn_queue_monitor' ) . '</a></div>';

        return $output;
    }

}
