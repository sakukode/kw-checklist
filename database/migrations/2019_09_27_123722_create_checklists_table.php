<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('object_domain')->nullable();
            $table->string('object_id')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_completed')->default(0);
            $table->datetime('completed_at')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->datetime('due')->nullable();
            $table->integer('due_interval')->nullable();
            $table->string('due_unit')->nullable();
            $table->integer('urgency')->nullable();            
            $table->nullableTimestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('template_id')->nullable();
            $table->foreign('template_id')->references('id')->on('templates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('checklists');
    }
}
