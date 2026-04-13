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
        Schema::create('modalidades', function (Blueprint $table) {
            $table->id();
            $table->string('aet')->nullable();
            $table->string('department')->nullable();
            $table->string('centre')->nullable();
            $table->string('location')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->boolean('syngo')->nullable()->default(false);
            $table->text('observations')->nullable();
            $table->string('model')->nullable();
            $table->string('modalidad')->nullable();
            $table->string('machine')->nullable();
            $table->string('station')->nullable();
            $table->date('request_date')->nullable();
            $table->json('extra_data')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modalidades');
    }
};
