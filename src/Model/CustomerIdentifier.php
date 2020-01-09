<?php

namespace Sminnee\CustomerDb\Model;

use SilverStripe\ORM\DataObject;

class CustomerIdentifier extends DataObject {
    private static $table_name = 'CustomerIdentifier';

    private static $db = [
        'AnonID' => 'Varchar(192)',
        'LastUsed' => 'Datetime',
    ];

    private static $has_one = [
        'Customer' => Customer::class,
    ];


    private static $summary_fields = [
        'AnonID',
        'LastUsed',
    ];
}
