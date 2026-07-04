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
        Schema::table('users', function (Blueprint $table) {
            // Contact number captured when staff are added to an organisation.
            $table->string('phone')->nullable()->after('email');

            // Tax PIN is maintained from the user's own profile, mainly for billing.
            $table->string('tax_pin')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'tax_pin']);
        });
    }
};
