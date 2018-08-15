<?php

use Illuminate\Database\Seeder;

class PatientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        for ($i = 0; $i < 100 ; $i++) {
//            DB::table('patients')->insert([
//                'name' => str_random(10),
//                'email' => str_random(10) . '@gmail.com'
//            ]);
//        }
        factory(App\Models\Patients::class, 500)->create();
    }
}
