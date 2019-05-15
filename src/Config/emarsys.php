<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bronto Contact Field Mappings
    |--------------------------------------------------------------------------
    |
    | Because Bronto uses a custom identifier for accessing and updating fields,
    | you need to run the contact service's outputFields method first and past the output in here.
    | Example below.
    |
    */

    'field_mappings' => [
        'firstName' => '0bce03e9000000000000000000000001cb4a',
        'lastName' => '0bce03e9000000000000000000000001cb4c'
    ],

    /*
    |--------------------------------------------------------------------------
    | Bronto Contact Field to Bronto Contact Object Mappings
    |--------------------------------------------------------------------------
    |
    | Now we map the properties of the SDK's Contact object to the field mappings
    | above. The values on the right should match the mapping names above. The values
    | here are from the MJ Bale Bronto contact fields, and are just an example.
    |
    */

    'contact_mappings' => [
        'id' => 'id',
        'firstName' => 'firstname',
        'lastName' => 'lastname',
        'gender' => 'gender',
        'birthday' => 'birthday',
        'companyName' => 'Company',
        'jobTitle' => 'Job_Title',
        'phoneHome' => 'Phone_number',
        'phoneMobile' => 'phone_mobile',
        'salutation' => 'salutation',
        'address1' => 'address1',
        'address2' => 'address2',
        'city' => 'city',
        'suburb' => 'suburb',
        'state' => 'state_province',
        'postCode' => 'postal_code',
        'creationDate' => 'Person_created_date',
    ],

];
