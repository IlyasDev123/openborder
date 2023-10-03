<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAppointmentTypeToConsultationBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consultation_bookings', function (Blueprint $table) {
            $table->string('appointment_type')->default('English');
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
            $table->dropColumn('appointment_type');
        });
    }
}
