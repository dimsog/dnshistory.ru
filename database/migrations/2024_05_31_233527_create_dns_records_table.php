<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dns_records', function (Blueprint $table) {
            $table->id();
            $table->integer('dns_id');
            $table->string('type', 6);
            $table->string('class', 6);
            $table->string('value', 128);

            $table->foreign('dns_id')
                ->references('id')
                ->on('dns')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dns_records');
    }
};
