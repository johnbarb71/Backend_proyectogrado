<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sucursal;

class SucursalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Sucursal::create([
            "nombre" => "Chimita",
            "direccion" => "km 4 Via Girón - Chimita",
            "telefono" => "6761111"
        ]);
        Sucursal::create([
            "nombre" => "Guarín 2",
            "direccion" => "Cra 33 # 35",
            "telefono" => "6761111"
        ]);
    }
}
