<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'locked_by')) {
                $table->foreignId('locked_by')->nullable()->after('updated_by')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('documents', 'locked_at')) {
                $table->timestamp('locked_at')->nullable()->after('locked_by');
            }
        });
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'locked_at')) {
                $table->dropColumn('locked_at');
            }
            if (Schema::hasColumn('documents', 'locked_by')) {
                $table->dropConstrainedForeignId('locked_by');
            }
        });
    }
};
