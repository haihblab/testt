<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 191)->require();
            $table->integer('category_id');
            $table->timestamp('due_date');
            $table->integer('manager_id');
            $table->integer('user_id');
            $table->string('status_manager', 191)->require();
            $table->string('status_admin', 191)->require();
            $table->string('content')->require();
            $table->integer('priority');
            $table->string('comments');
            $table->timestamps();
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
        Schema::dropIfExists('requests');
    }
}
