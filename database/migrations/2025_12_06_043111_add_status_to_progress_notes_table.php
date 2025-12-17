<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('progress_notes', function (Blueprint $table) {
            // 'pending', 'approved', 'rejected'
            $table->string('status')->default('pending')->after('note');
        });
    }

    public function down(): void
    {
        Schema::table('progress_notes', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
