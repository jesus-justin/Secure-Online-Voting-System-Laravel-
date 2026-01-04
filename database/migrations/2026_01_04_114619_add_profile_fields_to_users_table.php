<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('email_notifications')->default(true)->after('verified_at');
            $table->boolean('sms_notifications')->default(false)->after('email_notifications');
            $table->string('phone_number')->nullable()->after('sms_notifications');
            $table->string('avatar')->nullable()->after('phone_number');
            $table->text('bio')->nullable()->after('avatar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['email_notifications', 'sms_notifications', 'phone_number', 'avatar', 'bio']);
        });
    }
};
