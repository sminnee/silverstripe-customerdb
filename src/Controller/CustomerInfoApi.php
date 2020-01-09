<?php

namespace Sminnee\CustomerDb\Controller;

use SilverStripe\Control\Controller;
use Sminnee\CustomerDb\Model\Customer;
use SilverStripe\Control\HTTPResponse;

class CustomerInfoApi extends Controller
{
    function index() {
        $identifier = $this->getRequest()->param('Identifier');
        if (!$identifier) {
            return new HTTPResponse('Please pass identifier', 404);
        }

        $customer = Customer::byAnonId($identifier, false);
        if (!$customer) {
            return new HTTPResponse('Bad identifier', 404);
        }

        $response = new HTTPResponse($customer->Properties);
        $response->addHeader('Content-Type', 'application/json');
        $response->addHeader('Access-Control-Allow-Origin', '*');
        return $response;
    }
}
