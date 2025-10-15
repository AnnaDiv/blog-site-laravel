<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('conversation_user', function (Blueprint $table) {
        $table->renameColumn('user_id', 'sender_id');
    });
}

public function down(): void
{
    Schema::table('conversation_user', function (Blueprint $table) {
        $table->renameColumn('sender_id', 'user_id');
    });
}
};