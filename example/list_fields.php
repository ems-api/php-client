<?php

// require the setup which has registered the autoloader
require_once __DIR__ . '/setup.php';

// CREATE THE ENDPOINT
$endpoint = new EmsApi\Endpoint\ListFields();

/*===================================================================================*/

// GET ALL ITEMS
$response = $endpoint->getFields('LIST-UNIQUE-ID');

// DISPLAY RESPONSE
echo '<pre>';
print_r($response->body);
echo '</pre>';
/*===================================================================================*/

// GET ONE ITEM
$response = $endpoint->getField('LIST-UNIQUE-ID', 'FIELD-ID');

// DISPLAY RESPONSE
echo '<pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// CREATE A LIST FIELD
$response = $endpoint->create('LIST-UNIQUE-ID', [
    'type'          => 'dropdown',
    'label'         => 'Text Label',
    'tag'           => 'DROPDOWN',
    'required'      => 'no',
    'visibility'    => 'visible',
    'sort_order'    => 0,
    'help_text'     => 'Help',
    'default_value' => '',
    'description'   => 'Description',
    'options'       => [
        [
            'name'  => 'Option1',
            'value' => 'Value1'
        ],
        [
            'name'  => 'Option2',
            'value' => 'Value2'
        ],
    ]
]);

// DISPLAY RESPONSE
echo '<hr /><pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// UPDATE LIST FIELD
$response = $endpoint->update('LIST-UNIQUE-ID', 'FIELD-ID', [
    'type'          => 'dropdown',
    'label'         => 'Text Label updated',
    'tag'           => 'DROPDOWN',
    'required'      => 'no',
    'visibility'    => 'visible',
    'sort_order'    => 0,
    'help_text'     => 'Help',
    'default_value' => '',
    'description'   => 'Description',
    'options'       => [
        [
            'name'  => 'Option1',
            'value' => 'Value1'
        ],
        [
            'name'  => 'Option2',
            'value' => 'Value2'
        ],
    ]
]);

// DISPLAY RESPONSE
echo '<hr /><pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// Delete a FIELD
$response = $endpoint->delete('LIST-UNIQUE-ID', 'FIELD-ID');

// DISPLAY RESPONSE
echo '<hr /><pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/

// GET THE LIST FIELD TYPES
$response = $endpoint->getListFieldTypes();

// DISPLAY RESPONSE
echo '<pre>';
print_r($response->body);
echo '</pre>';

/*===================================================================================*/
