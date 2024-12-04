<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuracions', function (Blueprint $table) {
            $table->id(); // id primary key with auto increment
            $table->string('configuracion', 254)->nullable(); // varchar(254) nullable
            $table->string('descripcion', 254)->nullable(); // varchar(254) nullable
            $table->integer('numero')->nullable()->default(0); // int(11) nullable default 0
            $table->integer('numero2')->nullable()->default(0); // int(11) nullable default 0
            $table->decimal('decimal', 10, 4)->nullable()->default(0.0000); // decimal(10,4) nullable default 0.0000
            $table->decimal('decimal2', 10, 4)->nullable()->default(0.0000); // decimal(10,4) nullable default 0.0000
            $table->dateTime('fecha')->nullable(); // datetime nullable
            $table->text('texto')->nullable(); // text nullable
            $table->tinyInteger('estado')->nullable()->default(0); // tinyint(4) nul
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
