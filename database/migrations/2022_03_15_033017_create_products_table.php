<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('codigo1',255);
            $table->string('codigo2',255)->nullable();
            $table->unsignedBigInteger('linea');
            $table->foreign('linea')->references('id')->on('proveedors')->onDelete('restrict')->onUpdate('restrict');
            $table->string('nombre',255);
            $table->bigInteger('paqxcaja')->nullable();
            $table->bigInteger('unixcaja')->nullable();
            $table->bigInteger('paqxdisp')->nullable();
            $table->date('fecha')->nullable();
            $table->decimal('estado')->nullable();
            $table->timestamps();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';

            
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
