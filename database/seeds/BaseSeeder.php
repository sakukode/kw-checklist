<?php

use Illuminate\Database\Seeder;

class BaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory('App\Template')->create()->each(function ($template) {
            factory('App\Checklist', 20)->create(['template_id' => $template->id])->each(function ($checklist) {
                factory('App\Item', 5)->create(['checklist_id' => $checklist->id]);     
            });
        });             
    }
}
