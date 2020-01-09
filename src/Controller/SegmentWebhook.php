<?php

namespace Sminnee\CustomerDb\Controller;

use SilverStripe\Control\Controller;
use Sminnee\CustomerDb\Model\Customer;

class SegmentWebhook extends Controller
{
    public function index()
    {
        $request = $this->getRequest()->getBody();

        $log = file_get_contents(BASE_PATH . '/segment.log');
        $log .= "[" . date('Y-m-d H:i:s') . "]\n" . $request . "\n";
        file_put_contents(BASE_PATH . '/segment.log', $log);

        $this->processSegmentPayload(json_decode($request, true));

        echo 'logged';
    }

    protected function processSegmentPayload(array $data)
    {
        if (!empty($data['userId'])) {
            $customer = Customer::byUserId($data['userId'], $data['anonymousId']);
        } else {
            $customer = Customer::byAnonId($data['anonymousId']);
        }

        $eventProperties = isset($data['properties']) ? $data['properties'] : [];
        $eventContext = isset($data['context']) ? $data['context'] : [];

        switch($data['type']) {
            case 'page':
                // Removed duplicated data
                unset($eventContext['page']);
                $this->processEvent($customer, 'pageview', $data['timestamp'], $eventProperties, $eventContext);
                break;

            case 'track':
                $this->processEvent($customer, $data['event'], $data['timestamp'], $eventProperties, $eventContext);
                break;

            case 'identify':
                $eventTraits = isset($data['traits']) ? $data['traits'] : [];
                $this->processIdentify($customer, $data['timestamp'], $eventTraits, $eventContext);
                break;
        }

    }

    protected function processEvent(Customer $customer, $event, $datetime, $properties, $context)
    {
        $customer->logEvent($event, $datetime, $properties, $context);
    }

    protected function processIdentify(Customer $customer, $datetime, $traits, $context)
    {
        $customer->addProperties($traits);
    }
}
