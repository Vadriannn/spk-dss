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
        Schema::create('kriterias', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 45)->unique();
            $table->string('nama', 100);
            $table->text('deskripsi')->nullable();
            $table->enum('enum', ['benefit', 'cost'])->default('benefit');
            $table->decimal('bobot_default', 10, 4)->default(1);
            $table->timestamps();
        });

        Schema::create('alternatifs', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 45)->unique();
            $table->string('nama', 100);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('sesi_keputusans', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['draft', 'aktif', 'selesai'])->default('aktif');
            $table->foreignId('users_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('sesi_kriterias', function (Blueprint $table) {
            $table->id();
            $table->decimal('bobot', 10, 4);
            $table->enum('tipe', ['benefit', 'cost'])->default('benefit');
            $table->foreignId('sesi_keputusans_id')->constrained('sesi_keputusans')->cascadeOnDelete();
            $table->foreignId('kriterias_id')->constrained('kriterias')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('sesi_alternatifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_keputusans_id')->constrained('sesi_keputusans')->cascadeOnDelete();
            $table->foreignId('alternatifs_id')->constrained('alternatifs')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('penilaians', function (Blueprint $table) {
            $table->id();
            $table->decimal('nilai', 12, 4);
            $table->foreignId('sesi_kriterias_id')->constrained('sesi_kriterias')->cascadeOnDelete();
            $table->foreignId('sesi_alternatif_id')->constrained('sesi_alternatifs')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['sesi_kriterias_id', 'sesi_alternatif_id'], 'penilaians_kriteria_alternatif_unique');
        });

        Schema::create('riwayat_perhitungans', function (Blueprint $table) {
            $table->id();
            $table->dateTime('waktu');
            $table->foreignId('users_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sesi_keputusans_id')->constrained('sesi_keputusans')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('ideal_perhitungans', function (Blueprint $table) {
            $table->id();
            $table->decimal('ideal_positif', 14, 6);
            $table->decimal('ideal_negatif', 14, 6);
            $table->foreignId('sesi_kriterias_id')->constrained('sesi_kriterias')->cascadeOnDelete();
            $table->foreignId('riwayat_perhitungans_id')->constrained('riwayat_perhitungans')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('detail_perhitungans', function (Blueprint $table) {
            $table->id();
            $table->decimal('nilai_awal', 14, 6);
            $table->decimal('nilai_normalisasi', 14, 6);
            $table->decimal('nilai_terbobot', 14, 6);
            $table->foreignId('riwayat_perhitungans_id')->constrained('riwayat_perhitungans')->cascadeOnDelete();
            $table->foreignId('sesi_kriterias_id')->constrained('sesi_kriterias')->cascadeOnDelete();
            $table->foreignId('sesi_alternatif_id')->constrained('sesi_alternatifs')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('hasil_perhitungans', function (Blueprint $table) {
            $table->id();
            $table->decimal('jarak_positif', 14, 6);
            $table->decimal('jarak_negatif', 14, 6);
            $table->decimal('nilai_preferensi', 14, 6);
            $table->integer('ranking');
            $table->foreignId('riwayat_perhitungans_id')->constrained('riwayat_perhitungans')->cascadeOnDelete();
            $table->foreignId('sesi_alternatif_id')->constrained('sesi_alternatifs')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_perhitungans');
        Schema::dropIfExists('detail_perhitungans');
        Schema::dropIfExists('ideal_perhitungans');
        Schema::dropIfExists('riwayat_perhitungans');
        Schema::dropIfExists('penilaians');
        Schema::dropIfExists('sesi_alternatifs');
        Schema::dropIfExists('sesi_kriterias');
        Schema::dropIfExists('sesi_keputusans');
        Schema::dropIfExists('alternatifs');
        Schema::dropIfExists('kriterias');
    }
};

