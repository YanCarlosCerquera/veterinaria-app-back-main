<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('veterinarios', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('second_name')->nullable();
            $table->string('last_name');
            $table->string('second_last_name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('telefono',10)->unique();
            $table->string('especialidad',100);
            $table->enum('tipo_identidad', ['C.C', 'Cédula de extranjería']);
            $table->string('numero_identidad');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('veterinarios');
    }
};
