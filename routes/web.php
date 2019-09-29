<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// Templates routes
$router->post('checklists/templates', 'TemplateController@store');
$router->patch('checklists/templates/{templateId}', 'TemplateController@update');
$router->delete('checklists/templates/{templateId}', 'TemplateController@destroy');
$router->post('checklists/templates/{templateId}/assigns', 'TemplateController@assigns');

// Items routes
$router->post('checklists/{checklistId}/items', 'ItemController@store');
$router->patch('checklists/{checklistId}/items/{itemId}', 'ItemController@update');
$router->delete('checklists/{checklistId}/items/{itemId}', 'ItemController@destroy');
$router->post('checklists/{checklistId}/items/_bulk', 'ItemController@mass_update');
$router->get('checklists/{checklistId}/items/{itemId}', [ 'as' => 'items.show', 'uses' => 'ItemController@show']);
$router->post('checklists/complete', 'ItemController@complete');
$router->post('checklists/incomplete', 'ItemController@incomplete');
$router->get('checklists/{checklistId}/items/', 'ItemController@index_by_checklist');
$router->get('checklists/items/', 'ItemController@index');

// Checklists routes
$router->post('checklists', 'ChecklistController@store');
$router->patch('checklists/{checklistId}', 'ChecklistController@update');
$router->delete('checklists/{checklistId}', 'ChecklistController@destroy');
$router->get('checklists', [ 'as' => 'checklists.index', 'uses' => 'ChecklistController@index' ]);
$router->get('checklists/{checklistId}', [ 'as' => 'checklists.show', 'uses' => 'ChecklistController@show' ]);
