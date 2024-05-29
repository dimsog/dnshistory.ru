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
        Schema::create('dns_worker_logs', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('server');
            $table->string('key');
            $table->unsignedInteger('errors')->default(0);

            $table->unique([
                'date',
                'server',
                'key',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dns_worker_logs');
    }
};
