<?php

namespace Sminnee\CustomerDb\Admin;

use SilverStripe\Admin\ModelAdmin;
use Sminnee\CustomerDb\Model\Customer;
use Sminnee\CustomerDb\Model\Profile;

class CustomerAdmin extends ModelAdmin
{
    private static $url_segment = 'customerdb';

    private static $menu_title = 'Customer DB';

    private static $title = 'Customer DB';

    private static $managed_models = [
        Customer::class,
        Profile::class,
    ];
}
