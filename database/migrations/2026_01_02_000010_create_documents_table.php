<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('doc_number')->unique();
            $table->string('title');
            $table->foreignId('document_type_id')->constrained('document_types')->cascadeOnDelete();
            $table->foreignId('classification_id')->constrained('classifications')->restrictOnDelete();
            $table->foreignId('origin_unit_id')->nullable()->constrained('organization_units')->nullOnDelete();
            $table->foreignId('workflow_definition_id')->nullable()->constrained('workflow_definitions')->nullOnDelete();
            $table->unsignedInteger('current_position')->default(0);
            $table->string('status')->default('draft'); // draft|in_review|approved|rejected|archived|disposed
            $table->foreignId('retention_policy_id')->nullable()->constrained('retention_policies')->nullOnDelete();
            $table->date('retention_until')->nullable();
            $table->boolean('legal_hold')->default(false);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
};
