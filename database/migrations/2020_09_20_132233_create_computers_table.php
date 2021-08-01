<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateComputersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('computers', function (Blueprint $table): void {
            $table->id();
            $table->timestamps();

            $table->foreignId('user_id')->constrained();
            $table->integer('serial');
            $table->text('vendor');
            $table->integer('model');
            $table->integer('type');
            $table->text('name');
            $table->timestamp('last_read')->nullable();
            $table->string('last_fingerprint')->nullable();

            $table->unique(['user_id', 'serial']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('computers');
    }
}
