<?php

use Illuminate\Database\Seeder;

class ChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory('App\Checklist', 20)->create()->each(function ($checklist) {
        	factory('App\Item', 5)->create(['checklist_id' => $checklist->id]);     
        });        
    }
}
