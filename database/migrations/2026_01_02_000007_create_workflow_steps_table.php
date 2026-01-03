<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_definition_id')->constrained('workflow_definitions')->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->string('key');
            $table->string('name');
            $table->string('assignee_type')->default('role'); // role|unit|user|registrar
            $table->string('assignee_value')->nullable(); // role key, unit id, or user id depending on type
            $table->boolean('requires_approval')->default(true);
            $table->boolean('allow_edit')->default(false);
            $table->timestamps();

            $table->unique(['workflow_definition_id', 'position']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('workflow_steps');
    }
};
