<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('person', function (Blueprint $table) {
            $table->increments('id');
            $table->string('APE_MATERNO');
            $table->string('APE_PATERNO');
            $table->string('COD_DEPARTAMENTO');
            $table->string('COD_DISTRITO');
            $table->string('COD_INTERIOR');
            $table->string('COD_NACIONALIDAD');
            $table->string('COD_PROVINCIA');
            $table->string('COD_SEXO');
            $table->string('COD_TIP_DOMICILIO');
            $table->string('COD_TIP_NUMERO');
            $table->string('COD_ZONA');
            $table->string('DES_DEPARTAMENTO');
            $table->string('DES_DISTRITO');
            $table->string('DES_PROVINCIA');
            $table->string('DIRECCION');
            $table->string('EMAIL');
            $table->string('ESTADO');
            $table->string('FEC_NACIMIENTO');
            $table->string('MCA_ASEGURADO');
            $table->string('MENSAJE');
            $table->string('NOMBRES');
            $table->string('NOM_INTERIOR');
            $table->string('NOM_TIP_DOMICILIO');
            $table->string('NOM_TIP_NUMERO');
            $table->string('NOM_ZONA');
            $table->string('NUM_DOCUMENTO');
            $table->string('REFERENCIA');
            $table->string('RESPUESTA');
            $table->string('TELEFONO');
            $table->string('TIP_DOCUMENTO');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('person');
    }
}
