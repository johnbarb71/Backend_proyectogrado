<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proveedor;
use JWTAuth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ProveedoresController extends Controller
{
    protected $user;
    public function __construct(Request $request)
    {
        $token = $request->header('Authorization');
        if($token != '')
            //En caso de que requiera autentifiación la ruta obtenemos el usuario y lo almacenamos en una variable, nosotros no lo utilizaremos.
            $this->user = JWTAuth::parseToken()->authenticate();
    }

    //Listar todos los proveedores
    public function index()
    {   
        return Proveedor::get();
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
        //Consulta el último ID
        $id = Proveedor::all()->last()->id;
        $id = $id+1;
        //Creamos el proveedor en la BD
        $proveedor = Proveedor::create([
            'id' => $id,
            'nombre' => $request->nombre
        ]);
        //Respuesta en caso de que todo vaya bien.
        return response()->json([
            'message' => 'Proveedor creado',
            'data' => $proveedor
        ], Response::HTTP_OK);
    }

    //Mostrar Proveedor por ID
    public function show($id)
    {
        //Bucamos el proveedor
        $proveedor = Proveedor::find($id);
        //Si el proveedor no existe devolvemos error no encontrado
        if (!$proveedor) {
            return response()->json([
                'message' => 'Producto no encontrado.'
            ], 404);
        }
        //Si hay proveedor lo devolvemos
        return $proveedor;
    }
    //Actualizar Proveedor
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
        //Buscamos el proveedor
        $proveedor = Proveedor::findOrfail($id);
        //Actualizamos el producto.
        $proveedor->update([
            'nombre' => $request->nombre
        ]);
        //Devolvemos los datos actualizados.
        return response()->json([
            'message' => 'Proveedor actualizado correctamente',
            'data' => $proveedor
        ], Response::HTTP_OK);
    }
}
