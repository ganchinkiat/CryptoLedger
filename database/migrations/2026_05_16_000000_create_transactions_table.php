<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('pair', 64)->nullable();
            $table->string('asset', 32)->nullable();
            $table->string('side', 16)->nullable();
            $table->decimal('price', 32, 8)->default(0);
            $table->decimal('quantity', 32, 8)->default(0);
            $table->decimal('amount', 32, 8)->default(0);
            $table->string('currency', 16)->nullable();
            $table->decimal('fee', 32, 8)->default(0);
            $table->string('fee_currency', 16)->nullable();
            $table->dateTime('transaction_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
