<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Consultation;
use Illuminate\Database\Seeder;


class ConsultationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       
    
        $adminUser = Consultation::updateOrCreate(['id' => 1],  [
        'type' => 'video_call',
        'price'=> 300,
        ]);
        $adminUser = Consultation::updateOrCreate(['id' => 2],  [
            'type' => 'phone_call',
            'price'=> 300,
        ]);
        $adminUser = Consultation::updateOrCreate(['id' => 3],  [
            'type' => 'in_person',
            'price'=> 300,
        ]);

    }
}
