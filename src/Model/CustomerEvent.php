<?php

namespace Sminnee\CustomerDb\Model;

use SilverStripe\ORM\DataObject;

class CustomerEvent extends DataObject {
    private static $table_name = 'CustomerEvent';

    private static $db = [
        'Timestamp' => 'Datetime',
        'EventType' => 'Varchar(128)',
        'Properties' => 'Text',
        'Context' => 'Text',
    ];

    private static $has_one = [
        'Customer' => Customer::class,
    ];

    private static $summary_fields = [
        'Timestamp',
        'EventType',
        'Properties',
        'Context',
    ];

    private static $default_sort = 'Timestamp DESC';
}
