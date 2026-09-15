<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            // Menambahkan kolom periode_id yang berelasi ke tabel periodes
            $table->foreignId('periode_id')->nullable()->after('id')->constrained('periodes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            // Menghapus foreign key dan kolom jika migration di-rollback
            $table->dropForeign(['periode_id']);
            $table->dropColumn('periode_id');
        });
    }
};
