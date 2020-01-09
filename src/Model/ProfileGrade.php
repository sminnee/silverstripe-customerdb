<?php

namespace Sminnee\CustomerDb\Model;

use SilverStripe\ORM\DataObject;

class ProfileGrade extends DataObject {
    private static $table_name = 'ProfileGrade';

    private static $db = [
        'Grade' => 'Int',
    ];

    private static $has_one = [
        'Profile' => Profile::class,
        'Customer' => Customer::class,
    ];
}
