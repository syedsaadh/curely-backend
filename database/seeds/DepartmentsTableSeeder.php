<?php

use Illuminate\Database\Seeder;

class DepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $departments = [
            [
                'name' => 'ENT',
                'desc' => 'Ear Nose Throat',
                'bed_count' => 25
            ],
            [
                'name' => 'MEDICINE',
                'desc' => 'Medicine',
                'bed_count' => 35
            ]
        ];
        foreach ($departments as $key => $value) {
            App\Models\Departments::create($value);
        }
    }
}
