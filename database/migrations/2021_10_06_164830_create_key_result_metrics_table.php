<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeyResultMetricsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('key_result_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('key_result_id')->constrained('key_results')->cascadeOnDelete();
            $table->unsignedMediumInteger('target', false)->default(0)->nullable();
            $table->unsignedMediumInteger('start', false)->default(0)->nullable();
            $table->unsignedMediumInteger('progress', false)->default(0)->nullable();
            $table->string('status')->default('ns')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('key_result_metrics');
    }
}
