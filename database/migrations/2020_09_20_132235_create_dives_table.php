<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateDivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('dives', function (Blueprint $table): void {
            $table->id();
            $table->timestamps();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->dateTime('date');
            $table->smallInteger('divetime')->nullable();
            $table->decimal('max_depth', 6, 3)->nullable();
            $table->string('country_code', 2)->nullable();
            $table->foreignId('place_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('computer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('fingerprint')->nullable();
            $table->json('samples')->nullable();

            $table->foreign('country_code')->on('countries')->references('iso2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('dives');
    }
}
