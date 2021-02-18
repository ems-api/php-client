<?php
 
// require the setup which has registered the autoloader
require_once __DIR__ . '/setup.php';

// CREATE THE ENDPOINT
$endpoint = new EmsApi\Endpoint\Templates();

/*===================================================================================*/

// GET ALL ITEMS
$response = $endpoint->getTemplates($pageNumber = 1, $perPage = 10);

// DISPLAY RESPONSE
echo '<pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// Search ALL ITEMS (available from MailWizz 1.4.4)
$response = $endpoint->searchTemplates($pageNumber = 1, $perPage = 10, [
    'name' => 'my template name'
]);

// DISPLAY RESPONSE
echo '<pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// GET ONE ITEM
$response = $endpoint->getTemplate('TEMPLATE-UNIQUE-ID');

// DISPLAY RESPONSE
echo '<hr /><pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// delete template
$response = $endpoint->delete('TEMPLATE-UNIQUE-ID');

// DISPLAY RESPONSE
echo '<hr /><pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// CREATE A NEW TEMPLATE
$rand = rand();
$response = $endpoint->create([
    'name'          => 'My API template ' . $rand,
    'content'       => file_get_contents(__DIR__ . '/template-example.html'),
    //'archive'     => file_get_contents(__DIR__ . '/template-example.zip'),
    'inline_css'    => 'no',// yes|no
]);

// DISPLAY RESPONSE
echo '<hr /><pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// UPDATE A TEMPLATE
$response = $endpoint->update('TEMPLATE-UNIQUE-ID', [
    'name'          => 'My API template - updated' . $rand,
    'content'       => file_get_contents(__DIR__ . '/template-example.html'),
    //'archive'     => file_get_contents(__DIR__ . '/template-example.zip'),
    'inline_css'    => 'no',// yes|no
]);

// DISPLAY RESPONSE
echo '<hr /><pre>';
print_r($response->body);
echo '</pre>';
