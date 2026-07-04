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

        Schema::create('uploads', function (Blueprint $table) {
            $table->id();
            $table->uuid()->nullable();
            $table->foreignId('organisation_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->nullableMorphs('owner');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('source_url');
            $table->string('thumbnail_url')->nullable();
            $table->string('file_name');
            $table->string('type', 50)->nullable();
            $table->string('extension', 10)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->foreignId('user_id')->nullable()->constrained();
            $table->timestamps();
            $table->string('deleted_to_url')->nullable();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
