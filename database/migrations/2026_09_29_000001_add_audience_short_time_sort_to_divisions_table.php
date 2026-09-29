<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('divisions', function (Blueprint $table) {
            $table->string('audience')->nullable()->after('title');
            $table->string('short_time')->nullable()->after('time');
            $table->unsignedInteger('sort')->default(0)->after('link');
        });
    }

    public function down(): void
    {
        Schema::table('divisions', function (Blueprint $table) {
            $table->dropColumn(['audience', 'short_time', 'sort']);
        });
    }
};
