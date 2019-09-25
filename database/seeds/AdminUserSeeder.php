<?php

use Illuminate\Database\Seeder;
use App\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    private function seedAdmin()
    {
    	$email = 'admin@test.com';
        $adminCheck = DB::table('users')->where('email', $email)->first();

        if ($adminCheck==false) {

            $credentials = [
            	'name' => 'Admin',
                'email' => $email,
                'password' => bcrypt('admin'),
                'is_admin' => 1
            ];

            $user = User::create($credentials);
        }
    }

    public function run()
    {
    	$this->seedAdmin();
    }
}
