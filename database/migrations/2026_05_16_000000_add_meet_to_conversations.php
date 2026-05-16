<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('conversations', 'meet_link')) {
                $table->string('meet_link')->nullable();
            }
            if (!Schema::hasColumn('conversations', 'meet_title')) {
                $table->string('meet_title')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn(['meet_link', 'meet_title']);
        });
    }
};
