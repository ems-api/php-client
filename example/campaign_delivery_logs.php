<?php
 
// require the setup which has registered the autoloader
require_once __DIR__ . '/setup.php';

// CREATE THE ENDPOINT
$endpoint = new EmsApi\Endpoint\CampaignDeliveryLogs();

/*===================================================================================*/

// GET ALL ITEMS
$response = $endpoint->getDeliveryLogs($campaignUid = 'CAMPAIGN-UNIQUE-ID', $pageNumber = 1, $perPage = 10);

// DISPLAY RESPONSE
echo '<pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// GET A DELIVERY LOG BY THE EMAIL MESSAGE ID
$response = $endpoint->getDeliveryLogByEmailMessageId('EMAIL-MESSAGE-ID');

// DISPLAY RESPONSE
echo '<hr /><pre>';
print_r($response->body);
echo '</pre>';
