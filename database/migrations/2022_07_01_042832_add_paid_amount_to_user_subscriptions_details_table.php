<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaidAmountToUserSubscriptionsDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_subscriptions_details', function (Blueprint $table) {
            $table->float('paid_amount',8 ,3)->default(0);
            $table->integer('remaining_recurring_payment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_subscriptions_details', function (Blueprint $table) {
            $table->dropColumn('remaining_recurring_payment');
            $table->dropColumn('paid_amount');
        });
    }
}
