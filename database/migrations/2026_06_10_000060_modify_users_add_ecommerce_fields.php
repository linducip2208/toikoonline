<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('city', 255)->nullable()->after('address');
            $table->string('postal_code', 255)->nullable()->after('city');
            $table->string('country', 255)->nullable()->after('postal_code');
            $table->decimal('balance', 20, 2)->default(0)->after('country');
            $table->boolean('banned')->default(false)->after('balance');
            $table->string('referral_code', 255)->nullable()->after('banned');
            $table->foreignId('referred_by')->nullable()->after('referral_code')->constrained('users')->nullOnDelete();
            $table->string('provider', 255)->nullable()->after('referred_by');
            $table->string('provider_id', 50)->nullable()->after('provider');
            $table->string('avatar', 256)->nullable()->after('provider_id');
            $table->string('avatar_original', 256)->nullable()->after('avatar');
            $table->string('device_token', 255)->nullable()->after('avatar_original');
            $table->integer('remaining_uploads')->default(0)->after('device_token');
            $table->string('user_type', 20)->default('customer')->after('remaining_uploads');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropColumn([
                'phone', 'address', 'city', 'postal_code', 'country',
                'balance', 'banned', 'referral_code', 'referred_by',
                'provider', 'provider_id', 'avatar', 'avatar_original',
                'device_token', 'remaining_uploads', 'user_type',
            ]);
        });
    }
};
