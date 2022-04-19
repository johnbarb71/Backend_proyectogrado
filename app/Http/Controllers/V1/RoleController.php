<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use JWTAuth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    protected $user;
    public function __construct(Request $request)
    {
        $token = $request->header('Authorization');
        if($token != '')
            //En caso de que requiera autentifiación la ruta obtenemos el usuario y lo almacenamos en una variable, nosotros no lo utilizaremos.
            $this->user = JWTAuth::parseToken()->authenticate();
    }

    //Listamos todos los roles
    public function index()
    {
        return Role::get();
    }

    public function store(Request $request)
    {
        //Validamos los datos
        $data = $request->only('nombre');
        $validator = Validator::make($data, [
            'nombre' => 'required|max:250|string'
        ]);
        //Si falla la validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        //Creamos el producto en la BD
        $product = Role::create([
            'nombre' => $request->nombre
        ]);
        //Respuesta en caso de que todo vaya bien.
        return response()->json([
            'message' => 'Role creado',
            'data' => $product
        ], Response::HTTP_OK);
    }

    public function show($id)
    {
        //Bucamos el role
        $role = Role::find($id);
        //Si el role no existe devolvemos error no encontrado
        if (!$role) {
            return response()->json([
                'message' => 'Producto no encontrado.'
            ], 404);
        }
        //Si hay producto lo devolvemos
        return $role;
    }
    
    public function update(Request $request, $id)
    {
        //Validación de datos
        $data = $request->only('nombre');
        $validator = Validator::make($data, [
            'nombre' => 'required|max:250|string'
        ]);
        //Si falla la validación error.
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        //Buscamos el role
        $role = Role::findOrfail($id);
        //Actualizamos el role.
        $role->update([
            'nombre' => $request->nombre
        ]);
        //Devolvemos los datos actualizados.
        return response()->json([
            'message' => 'Rol actualizado correctamente',
            'data' => $role
        ], Response::HTTP_OK);
    }

}
