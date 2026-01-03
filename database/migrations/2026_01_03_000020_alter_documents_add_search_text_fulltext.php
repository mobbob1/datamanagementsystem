<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'search_text')) {
                $table->longText('search_text')->nullable()->after('legal_hold');
            }
        });

        // Optional: fulltext index for MySQL fallback
        try {
            Schema::table('documents', function (Blueprint $table) {
                $table->fullText(['title', 'doc_number', 'search_text']);
            });
        } catch (\Throwable $e) {
            // Ignore if DB driver doesn't support fulltext
        }
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'search_text')) {
                $table->dropColumn('search_text');
            }
        });
    }
};
