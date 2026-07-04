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
        Schema::table('organisations', function (Blueprint $table) {
            $table->foreignId('industry_id')->nullable()->after('slug')->constrained()->nullOnDelete();
            $table->foreignId('country_id')->nullable()->after('industry_id')->constrained()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->after('country_id')->constrained()->nullOnDelete();

            $table->string('currency_code', 3)->nullable()->after('city_id');
            $table->foreign('currency_code')->references('code')->on('currencies')->nullOnDelete();

            $table->string('phone')->nullable()->after('currency_code');
            $table->string('email')->nullable()->after('phone');
            $table->text('address')->nullable()->after('email');
            $table->string('tax_pin')->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->dropForeign(['currency_code']);
            $table->dropConstrainedForeignId('industry_id');
            $table->dropConstrainedForeignId('country_id');
            $table->dropConstrainedForeignId('city_id');
            $table->dropColumn(['currency_code', 'phone', 'email', 'address', 'tax_pin']);
        });
    }
};
