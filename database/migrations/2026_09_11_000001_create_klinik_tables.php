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
        Schema::create('polis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_poli');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('dokters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('poli_id')->constrained('polis')->cascadeOnDelete();
            $table->string('no_str')->unique();
            $table->string('spesialisasi');
            $table->string('foto')->nullable();
            $table->timestamps();
        });

        Schema::create('pasiens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('no_rm')->unique();
            $table->string('nik')->unique();
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->string('no_telp');
            $table->timestamps();
        });

        Schema::create('obats', function (Blueprint $table) {
            $table->id();
            $table->string('nama_obat');
            $table->string('satuan');
            $table->integer('stok')->default(0);
            $table->integer('harga')->default(0);
            $table->timestamps();
        });

        Schema::create('jadwal_praktiks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dokter_id')->constrained('dokters')->cascadeOnDelete();
            $table->string('hari');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('kuota');
            $table->timestamps();
        });

        Schema::create('reservasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasiens')->cascadeOnDelete();
            $table->foreignId('dokter_id')->constrained('dokters')->cascadeOnDelete();
            $table->foreignId('jadwal_praktik_id')->nullable()->constrained('jadwal_praktiks')->nullOnDelete();
            $table->date('tanggal_reservasi');
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservasi_id')->constrained('reservasis')->cascadeOnDelete();
            $table->text('keluhan');
            $table->text('diagnosa');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservasi_id')->constrained('reservasis')->cascadeOnDelete();
            $table->bigInteger('total_bayar')->default(0);
            $table->string('metode_bayar')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        Schema::create('ulasans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ulasanable_id');
            $table->string('ulasanable_type');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->tinyInteger('rating')->unsigned();
            $table->text('komentar')->nullable();
            $table->timestamps();
        });

        Schema::create('lampirans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lampiranable_id');
            $table->string('lampiranable_type');
            $table->string('file_path');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('resep_obat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rekam_medis_id')->constrained('rekam_medis')->cascadeOnDelete();
            $table->foreignId('obat_id')->constrained('obats')->cascadeOnDelete();
            $table->string('dosis')->nullable();
            $table->integer('jumlah')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resep_obat');
        Schema::dropIfExists('lampirans');
        Schema::dropIfExists('ulasans');
        Schema::dropIfExists('pembayarans');
        Schema::dropIfExists('rekam_medis');
        Schema::dropIfExists('reservasis');
        Schema::dropIfExists('jadwal_praktiks');
        Schema::dropIfExists('obats');
        Schema::dropIfExists('pasiens');
        Schema::dropIfExists('dokters');
        Schema::dropIfExists('polis');
    }
};
