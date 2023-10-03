<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSpanDescriptionToPlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('plan_name_es')->nullable()->after('plan_name');
            $table->longText('description_es')->nullable()->after('description');
            $table->string('plan_type_es')->nullable()->after('plan_type');
            $table->string('recurring_period_es')->nullable()->after('recurring_period');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('plan_name_es');
            $table->dropColumn('description_es');
            $table->dropColumn('plan_type_es');
            $table->dropColumn('recurring_period_es');
        });
    }
}
