<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateEquipmentTankTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('equipment_tanks', function (Blueprint $table): void {
            $table->id();
            $table->timestamps();

            $table->foreignId('equipment_id')->constrained()->cascadeOnDelete();
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
    public function down(): void
    {
        Schema::dropIfExists('equipment_tanks');
    }
}
