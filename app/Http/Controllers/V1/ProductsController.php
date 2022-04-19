<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Inventario;
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
        $data = $request->only('codigo1','nombre','linea');
        $validator = Validator::make($data, [
            'codigo1' => 'required|numeric',
            'nombre' => 'required|max:250|string',
            'linea' => 'required|numeric'
        ]);
        //Si falla la validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        //Creamos el producto en la BD
        $product = Product::create([
            /* 'codigo' => $request->codigo, */
            'codigo1' => $request->codigo1,
            'nombre' => $request->nombre,
            'linea' => $request->linea,
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
        $data = $request->only('nombre', 'codigo1');
        $validator = Validator::make($data, [
            'nombre' => 'required|max:250|string',
            'codigo1' => 'required|max:250|string'
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
            'estado' => $request->estado,
            'codigo1' => $request->codigo1,
            'linea' => $request->linea
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
    /**
     * Mostrar Producto por Código.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function getProdCod($codigo1)
    {
        //Bucamos el producto
        $product = Product::where('codigo1',$codigo1)->get();
        //Si el producto no existe devolvemos error no encontrado
        if ($product->isEmpty()) {
            return response()->json([
                'message' => 'Producto no encontrado.'
            ], 404);
        }
        //Si hay producto lo devolvemos
        return $product;
    }
    /**
     * Método para ingresar cantidades de Gondola y Bodega.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function ActProdCod(Request $request, $codigo1, $id_sucursal)
    {
        //Validación de datos
        $data = $request->only('gondola','bodega');
        $validator = Validator::make($data, [
            'gondola' => 'required|integer|min:0',
            'bodega' => 'required|integer|min:0'
        ]);
        //Creación de variables y suma de estos para llenar campo en tabla BBDD
        $bod=$request->bodega;
        $gon=$request->gondola;
        $result=$bod+$gon;
        //Si falla la validación error.
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        //Buscamos el producto, actualizamos campos
        //$product = Product::where('codigo1',$codigo1)->update(['gondola' => $request->gondola, //'bodega'=>$request->bodega,'resultado'=>$result]);
        $product = Product::where('codigo1',$codigo1)->select('id')->get();
        $id_produc = $product{0}->id;
        $inventar1 = Inventario::where('id_products',$id_produc)->where('id_sucursal',$id_sucursal)->get();
        if(empty($inventar1{0})) {
            $inventario = Inventario::insert([
            'id_sucursal' => $id_sucursal,
            'id_products' => $id_produc,
            'gondola' => $request->gondola,
            'bodega' => $request->bodega,
            'resultado'=>$result
            ]);
            //Devolvemos los datos actualizados.
            return response()->json([
            'message' => 'Producto creado correctamente',
            'id_product' => $id_produc,
            'inventar1' => $inventar1,
            'inventario' => $inventario
            ], Response::HTTP_OK);
        }else{
            $inventario = Inventario::where('id_products',$id_produc)->where('id_sucursal',$id_sucursal)->update(['gondola' => $request->gondola,'bodega' => $request->bodega,'resultado'=>$result]);
            //Devolvemos los datos actualizados.
            return response()->json([
            'message' => 'Producto actualizado correctamente',
            'id_product' => $id_produc,
            'inventar1' => $inventar1,
            'inventario' => $inventario
            ], Response::HTTP_OK);
        }

        
    }
    //Obtenemos lista de productos según proveedor
    public function getProdxProvCod($linea)
    {
        //Bucamos el producto por proveedor
        $product = Product::where('linea',$linea)->get();
        //Si el producto no existe devolvemos error no encontrado
        if ($product->isEmpty()) {
            return response()->json([
                'message' => 'Producto no encontrado.'
            ], 404);
        }
        //Si hay producto lo devolvemos
        return $product;
    }

    //Busqueda por nombre
    public function buscarxnombre($nombre){
        $product = Product::where('nombre','LIKE','%'.$nombre.'%')->get();
        //Si el producto no existe devolvemos error no encontrado
        if ($product->isEmpty()) {
            return response()->json([
                'message' => 'Producto no encontrado.'
            ], 404);
        }
        //Si hay producto lo devolvemos
        return $product;
    }


    //Actualizamos por codigo
    public function getProdCodigo($codigo1,$id_sucursal)
    {
        //Bucamos el producto
        /*$product = Product::where('codigo1',$codigo1)
                    ->join('inventarios', 'products.id', '=', 'inventarios.id_products')
                    ->select('products.*', 'inventarios.gondola', 'inventarios.bodega')
                    ->get();
        */
        //$product = Inventario::where('id_products',$codigo1)->get();
        $product = Product::where('codigo1',$codigo1)->get();
        $id_produc = $product{0}->id;
        //$id_produc = '1';
        $inventario = Inventario::where('id_products',$id_produc)->where('id_sucursal',$id_sucursal)->get();
        //Si el producto no existe devolvemos error no encontrado
        if ($product->isEmpty()) {
            return response()->json([
                'message' => 'Producto no encontrado.'
            ], 404);
        }
        if ($inventario->isEmpty()) {
            //return $product;
            return response()->json(
                array(
                    'producto' => $product,
                    'inventario'=> 'null'
                ),200);
        }else{
            return response()->json(
                array(
                    'producto' => $product,
                    'inventario'=> $inventario
                ),200);
        }
        //Si hay producto lo devolvemos
        //return $product;
    }

    //Reporte de inventario
    public function informeCompleto($id_sucursal){
        $product = Inventario::where('id_sucursal',$id_sucursal)->where('cantidad','>', 0)
                    ->join('products', 'products.id', '=', 'inventarios.id_products')
                    ->select('products.nombre','products.codigo1', 'inventarios.gondola', 'inventarios.bodega','inventarios.resultado','inventarios.cantidad')
                    ->selectRaw('(inventarios.cantidad-inventarios.resultado) AS total')
                    ->orderBy('total', 'desc')
                    ->orderBy('inventarios.cantidad', 'desc')
                    ->get();
        if ($product->isEmpty()) {
            return response()->json([
                'message' => 'Producto no encontrado.'
            ], 404);
        }else{
            return response()->json(
                array(
                    'producto' => $product
                ),200);
        }
    }

    //Reporte de inventario por linea
    public function informeCompletoLinea($id_sucursal,$linea){
        $product = Inventario::where('id_sucursal',$id_sucursal)->where('cantidad','>', 0)->where('linea',$linea)
                    ->join('products', 'products.id', '=', 'inventarios.id_products')
                    ->select('products.nombre','products.codigo1', 'inventarios.gondola', 'inventarios.bodega','inventarios.resultado','inventarios.cantidad')
                    ->selectRaw('(inventarios.cantidad-inventarios.resultado) AS total')
                    ->orderBy('total', 'desc')
                    ->orderBy('inventarios.cantidad', 'desc')
                    ->get();
        if ($product->isEmpty()) {
            return response()->json([
                'message' => 'Producto no encontrado.'
            ], 404);
        }else{
            return response()->json(
                array(
                    'producto' => $product
                ),200);
        }
    }

    public function contadorACeros($id_sucursal)
    {
        $inventario = Inventario::where('id_sucursal',$id_sucursal)->update(['gondola' => 0,'bodega' => 0,'resultado'=>0,'cantidad'=>0]);
            //Devolvemos los datos actualizados.
            return response()->json([
            'message' => 'Todas las cantidades a cero de este proveedor',
            'inventario' => $inventario
            ], Response::HTTP_OK);
    }

}
