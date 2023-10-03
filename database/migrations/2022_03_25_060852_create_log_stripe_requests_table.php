<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogStripeRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_stripe_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('object_id');
            $table->string('type');
            $table->string('object');
            $table->longText('log');
            $table->timestamps();
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_stripe_requests');
    }
}
