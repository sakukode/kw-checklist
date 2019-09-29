<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Faker\Factory as Faker;

class ChecklistTest extends TestCase
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
    public function test_can_get_all_checklist()
    {
        $checklist = factory('App\Checklist', 3)->create();
        $response = $this->call('GET', '/checklists/');
        $this->assertEquals(200, $response->status());
        $this->seeJsonStructure([            
            'data' => [ '*' => 
                [
                    'type',
                    'id',
                    'attributes' => [
                        'object_domain',
                        'object_id',
                        'description',
                        'is_completed',
                        'completed_at',
                        'update_by',
                        'created_by',
                        'update_at',
                        'created_at',
                        'due',
                        'urgency',
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
     * Test Can Get Checklist With ID
     *
     * @return void
     */
    public function test_can_get_checklist()
    {
        $checklist = factory('App\Checklist')->create();
        $response = $this->call('GET', '/checklists/'.$checklist->id);
        $this->assertEquals(200, $response->status());
        $this->seeJsonStructure([            
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'object_domain',
                    'object_id',
                    'description',
                    'is_completed',
                    'completed_at',
                    'update_by',
                    'created_by',
                    'update_at',
                    'created_at',
                    'due',
                    'urgency',
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
    public function test_can_create_checklist()
    {
        $response = $this->call('POST', '/checklists', [
            'data' => [
                'attributes' => [
                    'object_domain' => $this->faker->domainWord,
                    'object_id'     => $this->faker->randomDigitNotNull,
                    'due'           => $this->faker->iso8601(),
                    'urgency'       => 1,
                    'description'   => $this->faker->sentence,
                    'items'         => [
                        $this->faker->text,
                        $this->faker->text,
                        $this->faker->text,
                    ],
                    'task_id'       => $this->faker->randomDigitNotNull,
                ],
            ],
        ]);
        $this->assertEquals(201, $response->status());
        $this->seeJsonStructure([            
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'object_domain',
                    'object_id',
                    'description',
                    'is_completed',
                    'completed_at',
                    'update_by',
                    'created_by',
                    'update_at',
                    'created_at',
                    'due',
                    'urgency',
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
    public function test_can_update_checklist()
    {
        $checklist = factory('App\Checklist')->create();
        $response = $this->call('PATCH', '/checklists/'.$checklist->id, [
            'data' => [
                'type' => 'checklists',
                'id' => $checklist->id,
                'attributes' => [
                    'object_domain' => $this->faker->domainWord,
                    'object_id'     => $this->faker->randomDigitNotNull,
                    'description'   => $this->faker->sentence,  
                    'is_completed'  => true,
                    'completed_at'  => null,
                    'created_at'    => $this->faker->iso8601(),
                ],
                "links" => [
                    'self' => route('checklists.show', ['checklistId' => $checklist->id])
                ]
            ],
        ]);
        $this->assertEquals(200, $response->status());
        $this->seeJsonStructure([            
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'object_domain',
                    'object_id',
                    'description',
                    'is_completed',
                    'completed_at',
                    'update_by',
                    'created_by',
                    'update_at',
                    'created_at',
                    'due',
                    'urgency',
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
    public function test_can_delete_checklist()
    {
        $checklist = factory('App\Checklist')->create();
        $response = $this->call('DELETE', '/checklists/'.$checklist->id);
        $this->assertEquals(204, $response->status());
    }
}
