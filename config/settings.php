<?php
// testing configuration
$config['Ggapi']['testing'] = array(
    'url' => 'https://staging.linkpt.net',
    'port' => '1129',
    'key' => '/full/path/to/key/12345678.pem',
    'configfile' => '12345678'
);

// live configuration
$config['Ggapi']['live'] = array(
    'url' => 'https://secure.linkpt.net',
    'port' => '1129',
    'key' => '/full/path/to/key/98765432.pem',
    'configfile' => '98765432'
);

// settings that don't change per order
$config['Ggapi']['settings'] = array(
    'ordertype' => 'PREAUTH',
    'transactionorigin' => 'ECI',
    'cvmindicator' => 'provided',
    'country' => 'US',
    'scountry' => 'US',
    //'result' => 'Good' Good|Decline|Duplicate for testing response
);

// map of local fields => GG API fields, required even if field names are the same
$config['Ggapi']['fields'] = array(
    'order_total' => 'chargetotal',
    'subtotal' => 'subtotal',
    'tax' => 'tax',
    'shipping' => 'shipping',
    'card_num' =>'cardnumber',
    'card_date' => array(
        'month' => 'cardexpmonth',
    ),
    'short_year' => 'cardexpyear',
    'card_cvc' => 'cvmvalue',
    'full_name' => 'name',
    'address' => 'address1',
    'city' => 'city',
    'state' => 'state',
    'zip' => 'zip',
    'shipping_full_name' => 'sname',
    'shipping_address' => 'saddress1',
    'shipping_city' => 'scity',
    'shipping_state' => 'sstate',
    'shipping_zip' => 'szip',
    'ip_address' => 'ip'
);

// GG API fields used: category => array('field) - (for building XML string)
$config['Ggapi']['apiFields'] = array(

    'orderoptions' => array(
        'ordertype',
        //'result' for testing on live server
    ),
    'creditcard' => array(
        'cardnumber',
        'cardexpmonth',
        'cardexpyear',
        'cvmvalue',
        'cvmindicator'
    ),
    'billing' => array(
        'name',
        'address1',
        'city',
        'state',
        'zip',
        'country'
    ),
    'shipping' => array(
        'name',
        'address1',
        'city',
        'state',
        'zip',
        'country'
    ),
    'transactiondetails' => array(
        'ip',
        'transactionorigin'
    ),
    'merchantinfo' => array(
        'configfile'
    ),
    'payment' => array(
        'chargetotal',
        'tax',
        'shipping',
        'subtotal'
    )
);