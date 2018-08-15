<?php
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'User can create edit delete data in the system'
            ],
            [
                'name' => 'doctor',
                'display_name' => 'Doctor',
                'description' => 'Doctor manages appointments'
            ]
        ];
        foreach ($roles as $key => $value) {
            Role::create($value);
        }

    }
}
