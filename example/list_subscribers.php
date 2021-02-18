<?php

// require the setup which has registered the autoloader
require_once __DIR__ . '/setup.php';

// CREATE THE ENDPOINT
$endpoint = new EmsApi\Endpoint\ListSubscribers();

/*===================================================================================*/

// GET ALL ITEMS
$response = $endpoint->getSubscribers('LIST-UNIQUE-ID', $pageNumber = 1, $perPage = 10);

// DISPLAY RESPONSE
echo '<pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// GET ONE ITEM
$response = $endpoint->getSubscriber('LIST-UNIQUE-ID', 'SUBSCRIBER-UNIQUE-ID');

// DISPLAY RESPONSE
echo '<hr /><pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// SEARCH BY EMAIL
$response = $endpoint->emailSearch('LIST-UNIQUE-ID', 'john.doe@doe.com');

// DISPLAY RESPONSE
echo '<hr /><pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// SEARCH BY EMAIL IN ALL LISTS
$response = $endpoint->emailSearchAllLists('john.doe@doe.com');

// DISPLAY RESPONSE
echo '<hr /><pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// SEARCH BY CUSTOM FIELDS IN A LIST
$response = $endpoint->searchByCustomFields('LIST-UNIQUE-ID', [
    'EMAIL' => 'john.doe@doe.com'
]);

// DISPLAY RESPONSE
echo '<hr /><pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// ADD SUBSCRIBER
$response = $endpoint->create('LIST-UNIQUE-ID', [
    'EMAIL'    => 'john.doe@doe.com', // the confirmation email will be sent!!! Use valid email address
    'FNAME'    => 'John',
    'LNAME'    => 'Doe'
]);

// DISPLAY RESPONSE
echo '<hr /><pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// ADD SUBSCRIBERS IN BULK (since MailWizz 1.8.1)
$response = $endpoint->createBulk('LIST-UNIQUE-ID', [
    [
        'EMAIL'    => 'john.doe-1@doe.com',
        'FNAME'    => 'John',
        'LNAME'    => 'Doe'
    ],
    [
        'EMAIL'    => 'john.doe-2@doe.com',
        'FNAME'    => 'John',
        'LNAME'    => 'Doe'
    ],
    [
        'EMAIL'    => 'john.doe-3@doe.com',
        'FNAME'    => 'John',
        'LNAME'    => 'Doe'
    ]
]);

// DISPLAY RESPONSE
echo '<hr /><pre>';
print_r($response->body);
echo '</pre>';


/*===================================================================================*/

// UPDATE EXISTING SUBSCRIBER
$response = $endpoint->update('LIST-UNIQUE-ID', 'SUBSCRIBER-UNIQUE-ID', [
    'EMAIL'    => 'john.doe@doe.com',
    'FNAME'    => 'John',
    'LNAME'    => 'Doe Updated'
]);

// DISPLAY RESPONSE
echo '<hr />';
echo '<pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// UPDATE EXISTING SUBSCRIBER BY EMAIL
$response = $endpoint->updateByEmail('LIST-UNIQUE-ID', 'john@doe.com', [
    'EMAIL'    => 'john.doe@doe.com',
    'FNAME'    => 'John',
    'LNAME'    => 'Doe Updated'
]);

// DISPLAY RESPONSE
echo '<hr />';
echo '<pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// CREATE / UPDATE EXISTING SUBSCRIBER
$response = $endpoint->createUpdate('LIST-UNIQUE-ID', [
    'EMAIL'    => 'john.doe@doe.com',
    'FNAME'    => 'John',
    'LNAME'    => 'Doe Updated Second time'
]);

// DISPLAY RESPONSE
echo '<hr /><pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// UNSUBSCRIBE existing subscriber, no email is sent, unsubscribe is silent
$response = $endpoint->unsubscribe('LIST-UNIQUE-ID', 'SUBSCRIBER-UNIQUE-ID');

// DISPLAY RESPONSE
echo '<hr /><pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// UNSUBSCRIBE existing subscriber by email address, no email is sent, unsubscribe is silent
$response = $endpoint->unsubscribeByEmail('LIST-UNIQUE-ID', 'john@doe.com');

// DISPLAY RESPONSE
echo '<hr /><pre>';
print_r($response->body);
echo '</pre>';
/*===================================================================================*/


// UNSUBSCRIBE existing subscriber from all lists, no email is sent, unsubscribe is silent
$response = $endpoint->unsubscribeByEmailFromAllLists('john@doe.com');

// DISPLAY RESPONSE
echo '<hr /><pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// DELETE SUBSCRIBER, no email is sent, delete is silent
$response = $endpoint->delete('LIST-UNIQUE-ID', 'SUBSCRIBER-UNIQUE-ID');

// DISPLAY RESPONSE
echo '<hr /><pre>';
print_r($response->body);
echo '</pre>';
/*===================================================================================*/

// DELETE SUBSCRIBER by email address, no email is sent, delete is silent
$response = $endpoint->deleteByEmail('LIST-UNIQUE-ID', 'john@doe.com');

// DISPLAY RESPONSE
echo '<hr /><pre>';
print_r($response->body);
echo '</pre>';
