<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('id');            
            $table->string('description')->nullable();
            $table->boolean('is_completed')->default(0);
            $table->datetime('completed_at')->nullable();            
            $table->datetime('due')->nullable();
            $table->integer('due_interval')->nullable();
            $table->string('due_unit')->nullable();            
            $table->integer('urgency')->nullable();                        
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('assignee_id')->nullable();
            $table->integer('task_id')->nullable();
            $table->unsignedBigInteger('checklist_id');
            $table->foreign('checklist_id')->references('id')->on('checklists');
            $table->nullableTimestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
