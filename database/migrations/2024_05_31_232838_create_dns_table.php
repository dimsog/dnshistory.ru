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
        Schema::create('dns', function (Blueprint $table) {
            $table->id();
            $table->integer('domain_id');
            $table->integer('date');
            $table->string('hash', 8);

            $table->foreign('domain_id')
                ->references('id')
                ->on('domains')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->unique(['domain_id', 'hash']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dns');
    }
};
