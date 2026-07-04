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
        // User display picture.
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_photo_path', 2048)->nullable()->after('email');
        });

        // Organisation logo.
        Schema::table('organisations', function (Blueprint $table) {
            $table->string('profile_photo_path', 2048)->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_photo_path');
        });

        Schema::table('organisations', function (Blueprint $table) {
            $table->dropColumn('profile_photo_path');
        });
    }
};
