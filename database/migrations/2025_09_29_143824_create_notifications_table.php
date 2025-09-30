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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('notification_owner_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('sender_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->string('content', 100);
            $table->string('place', 100);

            $table->string('link', 255);
            $table->tinyInteger('used')->default(0);

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
