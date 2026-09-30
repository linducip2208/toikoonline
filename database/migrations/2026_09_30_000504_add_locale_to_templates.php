<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->string('locale', 10)->default('id')->after('identifier');
        });
        Schema::table('sms_templates', function (Blueprint $table) {
            $table->string('locale', 10)->default('id')->after('identifier');
        });
    }

    public function down(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->dropColumn('locale');
        });
        Schema::table('sms_templates', function (Blueprint $table) {
            $table->dropColumn('locale');
        });
    }
};
