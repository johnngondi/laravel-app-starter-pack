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
        Schema::create('pending_actions', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('actionable');

            // People who can act (if one of them acts the task is deleted and no longer pending)
            $table->json('actors');

            // Clear notes on the resource.
            $table->text('notes');

            $table->string('action_type');

            $table->timestamp('due_at');
            $table->enum('priority', ['critical', 'high', 'medium', 'low'])->default('medium');

            // Link to resource, stored as relative but when shown on the UI is absolute.
            $table->text('resource_url');

            // Text shown on primary action button
            $table->string('action_button_title')->default('Open Task');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_actions');
    }
};
