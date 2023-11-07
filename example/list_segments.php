<?php

// require the setup which has registered the autoloader
require_once __DIR__ . '/setup.php';

// CREATE THE ENDPOINT
$endpoint = new EmsApi\Endpoint\ListSegments();

/*===================================================================================*/

// GET ALL ITEMS
$response = $endpoint->getSegments('LIST-UNIQUE-ID');

// DISPLAY RESPONSE
echo '<pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// GET ONE ITEM
$response = $endpoint->getSegment('LIST-UNIQUE-ID', 'SEGMENT-UNIQUE-ID');

// DISPLAY RESPONSE
echo '<pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// CREATE A LIST SEGMENT
$response = $endpoint->create('LIST-UNIQUE-ID', [
    // required
    'name'                => 'My list segment created from the API', // required
    'operator_match'      => 'any', // required (any/all)
    // optional
    'conditions'          => [
        [
            'field_id'    => '96', // required .
            'operator_id' => '3', // required .  See the API-URL/lists/segments/condition-operators endpoint
            'value'       => 'domain.com', // required
        ],
        [
            'field_id'    => '95', // required .
            'operator_id' => '4', // required .  See the API-URL/lists/segments/condition-operators endpoint
            'value'       => 'keyword', // required
        ]
    ],
    'campaign_conditions' => [
        [
            'action'                   => 'click', // required (click/open)
            'campaign_id'              => '100', // required
            'time_comparison_operator' => 'lte', // required (lte/lt/gte/gt/eq)
            'time_value'               => '3', // required
            'time_unit'                => 'day' // required (day/month/year)
        ],
        [
            'action'                    => 'open', // required (click/open)
            'campaign_id              ' => '99', // required
            'time_comparison_operator'  => 'gte', // required (lte/lt/gte/gt/eq)
            'time_value'                => '3', // required
            'time_unit'                 => 'month' // required (day/month/year)
        ]
    ],
]);

// DISPLAY RESPONSE
echo '<hr /><pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// UPDATE LIST SEGMENT
$response = $endpoint->update('LIST-UNIQUE-ID', 'SEGMENT-UNIQUE-ID', [
    // required
    'name'                => 'My list segment created from the API updated', // required
    'operator_match'      => 'any', // required (any/all)
    // optional
    'conditions'          => [
        [
            'field_id'    => '96', // required .
            'operator_id' => '3', // required .  See the API-URL/lists/segments/condition-operators endpoint
            'value'       => 'domain.com', // required
        ],
        [
            'field_id'    => '95', // required .
            'operator_id' => '4', // required .  See the API-URL/lists/segments/condition-operators endpoint
            'value'       => 'keyword', // required
        ]
    ],
    'campaign_conditions' => [
        [
            'action'                   => 'click', // required (click/open)
            'campaign_id'              => '100', // required
            'time_comparison_operator' => 'lte', // required (lte/lt/gte/gt/eq)
            'time_value'               => '3', // required
            'time_unit'                => 'day' // required (day/month/year)
        ],
        [
            'action'                   => 'open', // required (click/open)
            'campaign_id'              => '99', // required
            'time_comparison_operator' => 'gte', // required (lte/lt/gte/gt/eq)
            'time_value'               => '3', // required
            'time_unit'                => 'month' // required (day/month/year)
        ]
    ],
]);

// DISPLAY RESPONSE
echo '<hr /><pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// Delete a SEGMENT
$response = $endpoint->delete('LIST-UNIQUE-ID', 'SEGMENT-UNIQUE-ID');

// DISPLAY RESPONSE
echo '<hr /><pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// GET THE LIST SEGMENT CONDITION OPERATORS
$response = $endpoint->getConditionOperators();

// DISPLAY RESPONSE
echo '<pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/
