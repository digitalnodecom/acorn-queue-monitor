<?php

namespace DigitalNode\AcornQueueMonitor;

use DigitalNode\AcornQueueMonitor\Contracts\JobRepository;

if ( ! defined( 'WPINC' ) ) die;

if (!class_exists('\WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Table extends \WP_List_Table {
    CONST PER_PAGE = 20;

    public function __construct( $args = array() ) {
        parent::__construct(array(
            'singular' => 'Job',
            'plural'   => 'Jobs',
            'ajax'     => false
        ));
    }

    public function prepare_items() {
        /* @var JobRepository $jobRepository */
        $jobRepository = app()->make(JobRepository::class);

        $jobs = $jobRepository->getJobs(
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
            case 'payload':
                return '<code>' . $item->payload . '</code>';
            case 'reserved_at':
                return $item->reserved_at ?? 'n/a';
            case 'available_at':
                return $item->available_at ?? 'n/a';
            case 'created_at':
                return $item->created_at ?? 'n/a';
            default:
                return $item->{$column_name} ?? '';
        }
    }

    public function get_columns()
    {
        $columns = array(
            'id' => 'ID',
            'queue' => 'Queue',
            'payload' => 'Payload',
            'reserved_at' => 'Reserved At',
            'available_at' => 'Available At',
            'created_at' => 'Created At',
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
        echo '<div class="wrap"><h2>TEST</h2>';
        echo '<form method="POST">';
        $this->display();
        echo '</form>';
        echo '</div>';
    }
}
