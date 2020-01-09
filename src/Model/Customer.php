<?php

namespace Sminnee\CustomerDb\Model;

use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;

class Customer extends DataObject {
    private static $table_name = 'Customer';

    private static $db = [
        'UserID' => 'Varchar(192)',
        'Email' => 'Varchar(192)',
        'Properties' => 'Text',
    ];

    private static $has_many = [
        'Events' => CustomerEvent::class,
        'ProfileGrades' => ProfileGrade::class,
        'Identifiers' => CustomerIdentifier::class,
    ];

    private static $summary_fields = [
        'UserID',
        'FirstName' => ['title' => 'First Name'],
        'LastName' => ['title' => 'Last Name'],
        'Email',
        'AnonymousIDs' => ['title' => 'Anonymous IDs'],
        'PropertySummary' => ['title' => 'Properties'],
    ];

    /**
     * Create or find a customer by userId
     */
    public static function byUserId($userId, $anonId)
    {
        $customer = Customer::get()->filter(['UserID' => $userId])->first();

        if (!$customer) {
            $customer = new Customer();
            $customer->UserID = $userId;
            $customer->write();
        }

        $customer->registerAnonId($anonId);

        return $customer;
    }

    /**
     * Create or find a customer by userId
     */
    public static function byAnonId($anonId, $autoCreate = true)
    {
        // Find existing
        $existing = CustomerIdentifier::get()->filter(['AnonID' => $anonId])->sort('LastUsed', 'desc')->first();
        if ($existing) {
            return $existing->Customer();
        }

        // Create new
        if ($autoCreate) {
            $c = new Customer();
            $c->write();
            $c->registerAnonId($anonId);
            return $c;
        }
    }

    /**
     * Register the use of an anonymous ID by this customer
     */
    public function registerAnonId($anonId)
    {
        $existing = $this->Identifiers()->filter(['AnonID' => $anonId])->first();
        if ($existing) {
            DB::prepared_query("UPDATE \"CustomerIdentifier\" SET \"LastUsed\" = now() WHERE \"ID\"  = ?", [ $existing->ID ]);

        } else {
            $ci = new CustomerIdentifier();
            $ci->CustomerID = $this->ID;
            $ci->AnonID = $anonId;
            $ci->LastUsed = date('Y-m-d H:i:s');
            $ci->write();
        }
    }

    /**
     * Log an event
     */
    public function logEvent($event, $datetime, $properties, $context)
    {
        $ce = new CustomerEvent();
        $ce->CustomerID = $this->ID;
        $ce->EventType = $event;
        $ce->Timestamp = $datetime;
        $ce->Properties = json_encode($properties);
        $ce->Context = json_encode($context);
        $ce->write();
    }

    /**
     * Add properties
     */
    public function addProperties($properties)
    {
        $existing = json_decode($this->Properties, true);
        if (!is_array($existing)) {
            $existing = [];
        }
        $updated = array_merge($existing, $properties);
        $this->Properties = json_encode($updated);
        $this->Email = $updated['email'];
        $this->write();
    }

    public function getFirstName()
    {
        if (!$this->UserID) return '(Anonymous)';
        return $this->extractProperty('firstName');
    }
    public function getLastName()
    {
        return $this->extractProperty('lastName');
    }

    public function getAnonymousIDs() {
        return implode("\n", $this->Identifiers()->column('AnonID'));
    }

    public function getPropertySummary() {
        $props = json_decode($this->Properties, true);
        if ($props) {
            $propLines = [];
            foreach ($props as $k => $v) {
                $propLines[] = "$k = $v";
            }

            return implode("\n", $propLines);
        }
    }

    protected function extractProperty($name)
    {
        $props = json_decode($this->Properties, true);
        return (string)$props[$name];
    }

}
