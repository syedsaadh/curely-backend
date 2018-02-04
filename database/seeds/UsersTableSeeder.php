<?php
use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserTableSeeder::class);
        DB::table('users')->delete();

        $users = array(
            ['name' => 'Saad Hassan', 'email' => 'talksaad@yahoo.com', 'mobile' => '9560401880', 'password' => Hash::make('secret')],
            ['name' => 'Chris Sevilleja', 'email' => 'chris@scotch.io','mobile' => '9560401880', 'password' => Hash::make('secret')],
            ['name' => 'Holly Lloyd', 'email' => 'holly@scotch.io','mobile' => '9560401880', 'password' => Hash::make('secret')],
            ['name' => 'Adnan Kukic', 'email' => 'adnan@scotch.io','mobile' => '9560401880', 'password' => Hash::make('secret')],
        );

        // Loop through each user above and create the record for them in the database
        $user = new User();
        $user->name = "admin";
        $user->email = "admin@curely.com";
        $user->mobile = "9560401880";
        $user->password = Hash::make('password');
        $user->save();
        $user->roles()->attach(1);

        foreach ($users as $user)
        {
            User::create($user);
        }
    }
}
