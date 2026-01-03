<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('document_files', function (Blueprint $table) {
            if (!Schema::hasColumn('document_files', 'version')) {
                $table->unsignedInteger('version')->default(1)->after('size');
            }
            if (!Schema::hasColumn('document_files', 'is_current')) {
                $table->boolean('is_current')->default(true)->after('version');
            }
            if (!Schema::hasColumn('document_files', 'checksum')) {
                $table->string('checksum', 64)->nullable()->after('is_current');
            }
        });
    }

    public function down()
    {
        Schema::table('document_files', function (Blueprint $table) {
            if (Schema::hasColumn('document_files', 'checksum')) {
                $table->dropColumn('checksum');
            }
            if (Schema::hasColumn('document_files', 'is_current')) {
                $table->dropColumn('is_current');
            }
            if (Schema::hasColumn('document_files', 'version')) {
                $table->dropColumn('version');
            }
        });
    }
};
