<?php

namespace Sminnee\CustomerDb\Model;

use SilverStripe\ORM\DataObject;

class Profile extends DataObject {
    private static $table_name = 'Profile';

    private static $db = [
        'Name' => 'Varchar',
    ];

    private static $has_many = [
        'CustomerGrade' => ProfileGrade::class,
    ];
}
