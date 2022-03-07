<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use JWTAuth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    protected $user;
    public function __construct(Request $request)
    {
        $token = $request->header('Authorization');
        if($token != '')
            //En caso de que requiera autentifiación la ruta obtenemos el usuario y lo almacenamos en una variable, nosotros no lo utilizaremos.
            $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index()
    {
        //Listamos todos los productos
        return Product::get();
    }

    public function store(Request $request)
    {
        //Validamos los datos
        $data = $request->only('codigo','nombre','cantidad','estado');
        $validator = Validator::make($data, [
            'codigo' => 'required|numeric',
            'nombre' => 'required|max:250|string',
            'cantidad' => 'required|numeric',
            'estado' => 'required|numeric'
        ]);
        //Si falla la validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        //Creamos el producto en la BD
        $product = Product::create([
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'cantidad' => $request->cantidad,
            'estado' => $request->estado,
        ]);
        //Respuesta en caso de que todo vaya bien.
        return response()->json([
            'message' => 'Producto creado',
            'data' => $product
        ], Response::HTTP_OK);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Bucamos el producto
        $product = Product::find($id);
        //Si el producto no existe devolvemos error no encontrado
        if (!$product) {
            return response()->json([
                'message' => 'Producto no encontrado.'
            ], 404);
        }
        //Si hay producto lo devolvemos
        return $product;
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //Validación de datos
        $data = $request->only('nombre', 'cantidad', 'estado');
        $validator = Validator::make($data, [
            'nombre' => 'required|max:250|string',
            'cantidad' => 'required|numeric',
            'estado' => 'required|numeric'
        ]);
        //Si falla la validación error.
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        //Buscamos el producto
        $product = Product::findOrfail($id);
        //Actualizamos el producto.
        $product->update([
            'nombre' => $request->nombre,
            'cantidad' => $request->cantidad,
            'estado' => $request->estado,
        ]);
        //Devolvemos los datos actualizados.
        return response()->json([
            'message' => 'Producto actualizado correctamente',
            'data' => $product
        ], Response::HTTP_OK);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Buscamos el producto
        $product = Product::findOrfail($id);
        //Eliminamos el producto
        $product->delete();
        //Devolvemos la respuesta
        return response()->json([
            'message' => 'Producto eliminado correctamente'
        ], Response::HTTP_OK);
    }

}
