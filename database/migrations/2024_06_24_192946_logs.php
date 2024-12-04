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
        Schema::create('logs', function (Blueprint $table) {
            $table->id(); // id primary key with auto increment
            $table->integer('users_id')->nullable()->index(); // int(11) nullable, indexed
            $table->text('log')->nullable(); // text nullable
            $table->string('ip', 254)->nullable(); // varchar(254) nullable
            $table->dateTime('created_at')->nullable()->index(); // datetime nullable, indexed
            $table->dateTime('updated_at')->nullable(); // datetime nullable
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
