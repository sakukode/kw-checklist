<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Faker\Factory as Faker;

class ItemTest extends TestCase
{
    use DatabaseMigrations;    

    protected $faker;

    public function setUp() :void 
    {
        parent::setUp();
        $this->faker = Faker::create();
    }

    /**
     * Test Can Get all Checklist
     *
     * @return void
     */
    public function test_can_get_all_item()
    {
        $checklist = factory('App\Checklist')->create();
        $items      = factory('App\Item', 20)->create(['checklist_id' => $checklist->id]);

        $response = $this->call('GET', '/checklists/items');
        $this->assertEquals(200, $response->status());
        $this->seeJsonStructure([            
            'data' => [ '*' => 
                [
                    'type',
                    'id',
                    'attributes' => [
                        'description',
                        'is_completed',
                        'completed_at',
                        'update_by',
                        'update_at',
                        'created_at',
                        'due',
                        'urgency',
                        'assignee_id',
                        'task_id',
                    ],
                    'links' => [
                        'self'
                    ]
                ]
            ],
            'meta' => [
                'count',
                'total'
            ],
            'links' => [
                'first',
                'last',
                'next',
                'prev'
            ]
            
        ]);

    }

    /**
     * Test Can Get all Checklist
     *
     * @return void
     */
    public function test_can_get_all_item_given_checklists()
    {
        $checklist = factory('App\Checklist')->create();
        $items      = factory('App\Item', 3)->create(['checklist_id' => $checklist->id]);

        $response       = $this->call('GET', '/checklists/'.$checklist->id.'/items');
        $content_json   = $response->getContent();
        $content        = json_decode($content_json);

        $this->assertEquals(3, $content->meta->total);
        $this->assertEquals(200, $response->status());
        $this->seeJsonStructure([            
            'data' => [ '*' => 
                [
                    'type',
                    'id',
                    'attributes' => [
                        'description',
                        'is_completed',
                        'completed_at',
                        'update_by',
                        'update_at',
                        'created_at',
                        'due',
                        'urgency',
                        'assignee_id',
                        'task_id',
                    ],
                    'links' => [
                        'self'
                    ]
                ]
            ],
            'meta' => [
                'count',
                'total'
            ],
            'links' => [
                'first',
                'last',
                'next',
                'prev'
            ]
            
        ]);

    }

    /**
     * Test Can Get Item With ID
     *
     * @return void
     */
    public function test_can_get_item()
    {
        $checklist = factory('App\Checklist')->create();
        $item      = factory('App\Item')->create(['checklist_id' => $checklist->id]);

        $response = $this->call('GET', '/checklists/'.$checklist->id.'/items/'.$item->id);
        $this->assertEquals(200, $response->status());
        $this->seeJsonStructure([            
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'description',
                    'is_completed',
                    'completed_at',
                    'update_by',
                    'update_at',
                    'created_at',
                    'due',
                    'urgency',
                    'assignee_id',
                    'task_id',
                ],
                'links' => [
                    'self'
                ]
            ],
            
        ]);

    }
    
    /**
     * Test Can Create Checklist
     *
     * @return void
     */
    public function test_can_create_item()
    {
        $checklist = factory('App\Checklist')->create();

        $response = $this->call('POST', '/checklists/'.$checklist->id.'/items', [
            'data' => [
                'attributes' => [                                        
                    'description'   => $this->faker->sentence,
                    'due'           => $this->faker->iso8601(),
                    'urgency'       => 1,
                    'assignee_id'   => $this->faker->randomDigitNotNull,  
                ],
            ],
        ]);
        $this->assertEquals(201, $response->status());
        $this->seeJsonStructure([            
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'description',
                    'is_completed',
                    'completed_at',
                    'update_by',
                    'update_at',
                    'created_at',
                    'due',
                    'urgency',
                    'assignee_id',
                    'task_id',
                ],
                'links' => [
                    'self'
                ]
            ],
            
        ]);

    }

    /**
     * Test Can Update Checklist
     *
     * @return void
     */
    public function test_can_update_item()
    {
        $checklist = factory('App\Checklist')->create();
        $item      = factory('App\Item')->create(['checklist_id' => $checklist->id]);

        $response = $this->call('PATCH', '/checklists/'.$checklist->id.'/items/'.$item->id, [
            'data' => [                
                'attributes' => [
                    'description'   => $this->faker->sentence,
                    'due'           => $this->faker->iso8601(),
                    'urgency'       => 1,
                    'assignee_id'   => $this->faker->randomDigitNotNull,  
                ],               
            ],
        ]);
        $this->assertEquals(200, $response->status());
        $this->seeJsonStructure([            
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'description',
                    'is_completed',
                    'completed_at',
                    'update_by',
                    'update_at',
                    'created_at',
                    'due',
                    'urgency',
                    'assignee_id',
                    'task_id',
                ],
                'links' => [
                    'self'
                ]
            ],
            
        ]);

    }

    /**
     * Test Can Delete Checklist
     *
     * @return void
     */
    public function test_can_delete_item()
    {
        $checklist = factory('App\Checklist')->create();
        $item      = factory('App\Item')->create(['checklist_id' => $checklist->id]);

        $response = $this->call('DELETE', '/checklists/'.$checklist->id.'/items/'.$item->id);
        $this->assertEquals(204, $response->status());
    }

    /**
     * Test Action Complete Items
     *
     * @return void
     */
    public function test_can_complete_items()
    {
        $checklist = factory('App\Checklist')->create();

        $items = factory('App\Item', 3)->create(['checklist_id' => $checklist->id]);
        $data = [];

        foreach($items as $item) {
            $data[] = [ 'item_id' => $item->id ];
        }

        $response = $this->call('POST', '/checklists/complete', [
            'data' => $data,
        ]);
        $this->assertEquals(200, $response->status());
        $this->seeJsonStructure([            
            'data' => [ '*' => 
                [
                    'id',
                    'item_id',
                    'is_completed',
                    'checklist_id',
                ]    
            ],            
        ]);

    }

    /**
     * Test Action Incomplete Items
     *
     * @return void
     */
    public function test_can_incomplete_items()
    {
        $checklist = factory('App\Checklist')->create();

        $items = factory('App\Item', 3)->create(['checklist_id' => $checklist->id]);
        $data = [];

        foreach($items as $item) {
            $data[] = [ 'item_id' => $item->id ];
        }

        $response = $this->call('POST', '/checklists/incomplete', [
            'data' => $data,
        ]);
        $this->assertEquals(200, $response->status());
        $this->seeJsonStructure([            
            'data' => [ '*' => 
                [
                    'id',
                    'item_id',
                    'is_completed',
                    'checklist_id',
                ]    
            ],            
        ]);

    }

    /**
     * Test Action Update bulk Items
     *
     * @return void
     */
    public function test_can_update_bulk_items()
    {
        $checklist = factory('App\Checklist')->create();

        $items = factory('App\Item', 3)->create(['checklist_id' => $checklist->id]);
        $data = [];

        foreach($items as $item) {
            $data[] = [ 
                'id' => $item->id ,
                'action' => 'update',
                'attributes' => [
                    'description'   => $this->faker->sentence,
                    'due'           => $this->faker->iso8601(),
                    'urgency'       => $this->faker->randomDigitNotNull,
                ]
            ];
        }

        $response = $this->call('POST', '/checklists/'.$checklist->id.'/items/_bulk', [
            'data' => $data,
        ]);        

        $this->assertEquals(200, $response->status());
        $this->seeJsonStructure([            
            'data' => [ '*' => 
                [
                    'id',
                    'action',
                    'status',                    
                ]    
            ],            
        ]);

    }
}
