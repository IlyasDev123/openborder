<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->integer('package_id')->unsigned()->nullable();
            $table->string('plan_name');
            $table->string('image')->nullable();
            $table->string('slug')->unique();
            $table->string('stripe_price_id');
            $table->float('amount', 8, 2);
            $table->longText('description')->nullable();
            $table->integer('duration')->default(1);
            $table->enum('plan_type',['recurring', 'single'])->default('single');
            $table->enum('recurring_period',['year', 'month', 'day','every_month'])->default('month');
            $table->tinyInteger('status')->default(1)->comment('1: active, 0: inactive');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans');
    }
}
