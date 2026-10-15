<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokters', function (Blueprint $table) {
            $table->unsignedBigInteger('tarif_konsultasi')->default(50000);
            $table->unsignedBigInteger('tarif_pengecekan')->default(0);
        });

        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->string('status_resep')->default('menunggu');
            $table->foreignId('disiapkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disiapkan_pada')->nullable();
            $table->foreignId('diserahkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diserahkan_pada')->nullable();
        });

        Schema::table('pembayarans', function (Blueprint $table) {
            $table->unsignedBigInteger('biaya_konsultasi')->default(0);
            $table->unsignedBigInteger('biaya_pengecekan')->default(0);
            $table->unsignedBigInteger('total_obat')->default(0);
            $table->string('nomor_kuitansi')->nullable()->unique();
            $table->string('kuitansi_path')->nullable();
            $table->foreignId('dikonfirmasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dikonfirmasi_pada')->nullable();
        });

        Schema::create('rincian_pembayaran_obat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_id')->constrained('pembayarans')->cascadeOnDelete();
            $table->foreignId('obat_id')->nullable()->constrained('obats')->nullOnDelete();
            $table->string('nama_obat');
            $table->unsignedInteger('jumlah');
            $table->unsignedBigInteger('harga_satuan');
            $table->unsignedBigInteger('subtotal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rincian_pembayaran_obat');

        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropForeign(['dikonfirmasi_oleh']);
            $table->dropColumn([
                'biaya_konsultasi',
                'biaya_pengecekan',
                'total_obat',
                'nomor_kuitansi',
                'kuitansi_path',
                'dikonfirmasi_oleh',
                'dikonfirmasi_pada',
            ]);
        });

        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->dropForeign(['disiapkan_oleh']);
            $table->dropForeign(['diserahkan_oleh']);
            $table->dropColumn([
                'status_resep',
                'disiapkan_oleh',
                'disiapkan_pada',
                'diserahkan_oleh',
                'diserahkan_pada',
            ]);
        });

        Schema::table('dokters', function (Blueprint $table) {
            $table->dropColumn(['tarif_konsultasi', 'tarif_pengecekan']);
        });
    }
};