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
        Schema::table('gcash_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('transaction_type_id')->nullable();
            $table->foreign('transaction_type_id')
                ->references('id')
                ->on('transaction_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gcash_transaction', function (Blueprint $table) {
            //
        });
    }
};
