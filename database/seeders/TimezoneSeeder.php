<?php

namespace Database\Seeders;

use App\CommanFunctions\TimezoneData;
use App\Models\TimeZone;
use Illuminate\Database\Seeder;
use App\CommanFunctions\TimezoneList;

class TimezoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timezone = TimezoneData::class;
        TimeZone::insert($timezone::getTimezone());
    }
}
