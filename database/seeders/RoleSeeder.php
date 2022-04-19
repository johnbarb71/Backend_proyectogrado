<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            "nombre" => "Usuario",
            "codigo" => "xb36pvbtt6hd8dt"
        ]);
        Role::create([
            "nombre" => "Administrador",
            "codigo" => "xb36pvbtt6bq8vt"
        ]);
        Role::create([
            "nombre" => "Superusuario",
            "codigo" => "xb36pvbtt6crf46"
        ]);
    }
}
