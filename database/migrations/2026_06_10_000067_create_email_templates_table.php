<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('identifier', 100);
            $table->string('email_type', 255);
            $table->string('subject', 255);
            $table->text('default_text')->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('is_status_changeable')->default(1);
            $table->boolean('is_default_text_editable')->default(1);
            $table->timestamps();
        });

        DB::statement('ALTER TABLE email_templates CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    }

    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
