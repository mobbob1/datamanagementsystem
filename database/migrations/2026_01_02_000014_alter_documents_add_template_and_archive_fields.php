<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('template_version_id')->nullable()->after('document_type_id')->constrained('template_versions')->nullOnDelete();
            $table->json('form_data')->nullable()->after('template_version_id');
            $table->timestamp('archived_at')->nullable()->after('status');
            $table->timestamp('disposed_at')->nullable()->after('archived_at');
        });
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('template_version_id');
            $table->dropColumn(['form_data', 'archived_at', 'disposed_at']);
        });
    }
};
