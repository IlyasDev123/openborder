<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConsultationEndTimeToConsultationBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consultation_bookings', function (Blueprint $table) {
            $table->time('consultation_end_time')->nullable();
            $table->float('paid_amount', 8,2)->default(0.0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consultation_bookings', function (Blueprint $table) {
            $table->dropColumn('consultation_end_time');
            $table->dropColumn('paid_amount');
        });
    }
}
