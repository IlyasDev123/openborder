<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmigrationToConsultationBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consultation_bookings', function (Blueprint $table) {
            $table->string("emigration_type", 500)->nullable();
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
            $table->dropColumn("emigration_type");
        });
    }
}
