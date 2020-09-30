<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiveTanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dive_tanks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('dive_id')->constrained()->cascadeOnDelete();
            $table->smallInteger('volume')->unsigned()->nullable();
            $table->smallInteger('oxygen')->unsigned()->nullable();
            $table->smallInteger('pressure_begin')->unsigned()->nullable();
            $table->smallInteger('pressure_end')->unsigned()->nullable();
            $table->enum('pressure_type', ['bar', 'psi'])->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dive_tanks');
    }
}
