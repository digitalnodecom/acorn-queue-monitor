<?php

namespace DigitalNode\AcornQueueMonitor;

use DigitalNode\AcornQueueMonitor\Contracts\JobRepository;

class Admin {
    public function __construct(protected Table $table) {

    }

    public function queues_menu_page(){
        add_menu_page(
            'Queues',
            'Queues',
            'manage_options',
            'queues',
            function () {
                app()->make(Table::class)->renderTable();
            },
            'dashicons-clock',
            '7.1'
        );
    }
}
