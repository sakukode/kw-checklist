<?php

use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $checklist = factory('App\Checklist')->create();
        factory('App\Item', 20)->create(['checklist_id' => $checklist->id]);     
    }
}
