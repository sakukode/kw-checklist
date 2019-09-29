<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Faker\Factory as Faker;

class TemplateTest extends TestCase
{
    use DatabaseMigrations;    

    protected $faker;

    public function setUp() :void 
    {
        parent::setUp();
        $this->faker = Faker::create();
    }

    /**
     * Test Can Get all Template
     *
     * @return void
     */
    public function test_can_get_all_template()
    {
        factory('App\Template', 3)->create()->each(function($template) {
            factory('App\Checklist', 1)->create(['template_id' => $template->id])->each(function ($checklist) {
                factory('App\Item', 3)->create(['checklist_id' => $checklist->id]);     
            });
        });

        $response = $this->call('GET', '/checklists/templates/');
        $this->assertEquals(200, $response->status());
        $this->seeJsonStructure([            
            'data' => [ '*' => 
                [
                    'type',
                    'id',
                    'attributes' => [
                        'name',
                        'checklist' => [
                            'description',
                            'due_interval',
                            'due_unit',
                        ],
                        'items' => [ '*' => 
                            [
                                'description',
                                'urgency',
                                'due_interval',
                                'due_unit',
                            ],                        
                        ]
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
     * Test Can Get Template With ID
     *
     * @return void
     */
    public function test_can_get_template()
    {
        factory('App\Template')->create()->each(function($template) {
            factory('App\Checklist', 1)->create(['template_id' => $template->id])->each(function ($checklist) {
                factory('App\Item', 3)->create(['checklist_id' => $checklist->id]);     
            });
        });

        $template = App\Template::first();

        $response = $this->call('GET', '/checklists/templates/'.$template->id);        
        $this->assertEquals(200, $response->status());
        $this->seeJsonStructure([            
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'name',
                    'checklist' => [
                        'description',
                        'due_interval',
                        'due_unit',
                    ],
                    'items' => [ '*' => 
                        [
                            'description',
                            'urgency',
                            'due_interval',
                            'due_unit',
                        ],                        
                    ]
                ],
                'links' => [
                    'self'
                ]
            ],
            
        ]);

    }

    /**
     * Test Can Create Template
     *
     * @return void
     */
    public function test_can_create_template()
    {        
        $response = $this->call('POST', '/checklists/templates/', [
            'data' => [                
                'attributes' => [
                    'name' => $this->faker->name,
                    'checklist' => [
                        'description' => $this->faker->sentence,
                        'due_interval' => $this->faker->randomDigit,
                        'due_unit' => 'hour'
                    ],
                    'items' => [
                        [
                            'description'   => $this->faker->sentence,
                            'urgency'       => $this->faker->randomDigit,
                            'due_interval'  => $this->faker->randomDigit,
                            'due_unit'      => 'minute'
                        ],
                        [
                            'description'   => $this->faker->sentence,
                            'urgency'       => $this->faker->randomDigit,
                            'due_interval'  => $this->faker->randomDigit,
                            'due_unit'      => 'minute'
                        ]
                    ]
                ],                
            ],
        ]);
        $this->assertEquals(201, $response->status());
        $this->seeJsonStructure([            
            'data' => [                
                'id',
                'attributes' => [
                    'name',
                    'checklist' => [
                        'description',
                        'due_interval',
                        'due_unit',
                    ],
                    'items' => [ '*' => 
                        [
                            'description',
                            'urgency',
                            'due_interval',
                            'due_unit',
                        ],                        
                    ]  
                ],                
            ],
            
        ]);

    }

    /**
     * Test Can Update Template
     *
     * @return void
     */
    public function test_can_update_template()
    {        
        $template = factory('App\Template')->create();
        $response = $this->call('PATCH', '/checklists/templates/'.$template->id, [
            'data' => [                
                'attributes' => [
                    'name' => $this->faker->name,
                    'checklist' => [
                        'description' => $this->faker->sentence,
                        'due_interval' => $this->faker->randomDigit,
                        'due_unit' => 'hour'
                    ],
                    'items' => [
                        [
                            'description'   => $this->faker->sentence,
                            'urgency'       => $this->faker->randomDigit,
                            'due_interval'  => $this->faker->randomDigit,
                            'due_unit'      => 'minute'
                        ],
                        [
                            'description'   => $this->faker->sentence,
                            'urgency'       => $this->faker->randomDigit,
                            'due_interval'  => $this->faker->randomDigit,
                            'due_unit'      => 'minute'
                        ]
                    ]
                ],                
            ],
        ]);        

        $this->assertEquals(200, $response->status());
        $this->seeJsonStructure([            
            'data' => [                
                'id',
                'attributes' => [
                    'name',
                    'checklist' => [
                        'description',
                        'due_interval',
                        'due_unit',
                    ],
                    'items' => [ '*' => 
                        [
                            'description',
                            'urgency',
                            'due_interval',
                            'due_unit',
                        ],                        
                    ]  
                ],                
            ],
            
        ]);

    }

    /**
     * Test Can Delete Template
     *
     * @return void
     */
    public function test_can_delete_template()
    {
        $template = factory('App\Template')->create();
        $response = $this->call('DELETE', '/checklists/templates/'.$template->id);
        $this->assertEquals(204, $response->status());
    }
}
