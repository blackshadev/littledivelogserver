<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('users', static function (Blueprint $blueprint): void {
            $blueprint->string('origin')->default('https://divelog.littledev.nl');
        });
    }

    public function down(): void
    {
        Schema::table('users', static function (Blueprint $blueprint): void {
            $blueprint->dropColumn('origin');
        });
    }
};
