<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User_sucursal;


class UserSucursalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User_sucursal::create([
            "id_user" => 1,
            "id_sucursal" => 1
        ]);
        User_sucursal::create([
            "id_user" => 2,
            "id_sucursal" => 2
        ]);
    }
}
