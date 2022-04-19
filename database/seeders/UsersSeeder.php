<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Vaciar la tabla
        //User::truncate();
        //Crear contraseña encriptada
        $password = bcrypt('123456');
        //Crear usuario
        User::create([
            "name" => "Administrador",
            "email" => "johnbarb71@gmail.com",
            "password" => $password,
            "role" => 3,
            "estado" => 1
        ]);
        User::create([
            "name" => "Jhon H. Barbosa G.",
            "email" => "johnbarb@hotmail.com",
            "password" => $password,
            "role" => 2,
            "estado" => 1
        ]);

    }
}
