<?php

use Illuminate\Database\Seeder;
use \App\Models\VitalSigns;
class VitalSignsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $signs = array(
            ['name' => 'Weight', 'desc' => 'Weight', 'unit' => 'kg', 'column_name' => 'weight'],
            ['name' => 'Blood Pressure', 'desc' => 'Blood Pressure', 'unit' => 'mmHg', 'column_name' => 'blood_pressure'],
            ['name' => 'Pulse', 'desc' => 'Pulse', 'unit' => 'Heart beats/min', 'column_name' => 'pulse'],
            ['name' => 'Temperature', 'desc' => 'Temperature', 'unit' => '°F', 'column_name' => 'temperature']
        );
        foreach ($signs as $sign)
        {
            VitalSigns::create($sign);
            //$name = $sign['column_name'];
            //Schema::table('appointment_vital_signs', function ($table) use ($name) {
              //  $table->string($name)->nullable();
            //});
        }
    }
}
