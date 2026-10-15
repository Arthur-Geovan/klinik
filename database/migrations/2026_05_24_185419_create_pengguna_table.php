<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
        $table->id(); // Ini untuk ID otomatis
        $table->string('nama');
        $table->string('alamat');
        $table->string('email')->unique();
        $table->string('password');
        $table->string('peran');
        $table->timestamps(); // Ini akan membuat kolom created_at dan updated_at secara otomatis
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};
