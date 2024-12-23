<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admins')->insert(
        [
            'user_id' => '1',
            'photo' => 'https://www.shutterstock.com/image-vector/user-icon-trendy-flat-style-600nw-418179856.jpg',
            "phone"=>"1234567890",
            "address"=>"1234 Admin Street",
            "retired"=>false
        ]);
    }
}
