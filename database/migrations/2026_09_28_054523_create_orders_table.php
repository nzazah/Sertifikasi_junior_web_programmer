<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // pembeli
            $table->text('address');
            $table->string('payment_proof')->nullable();
            $table->enum('status', [
                'menunggu_bayar',
                'menunggu_konfirmasi',
                'diproses',
                'dikirim',
                'selesai',
                'dibatalkan',
            ])->default('menunggu_bayar');
            $table->unsignedBigInteger('total');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};