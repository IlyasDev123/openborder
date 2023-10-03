<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsultationBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consultation_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade')->nullable();
            $table->foreignId('consultation_id')->constrained()->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->date('date')->nullable();
            $table->time('consultation_time')->nullable();
            $table->string('consultation_with')->nullable();
            $table->enum('status',['active', 'inactive'])->default('active');
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
        Schema::dropIfExists('consultation_bookings');
    }
}
