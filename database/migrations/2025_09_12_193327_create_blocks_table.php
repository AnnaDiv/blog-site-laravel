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
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();

            // string columns that store nicknames
            $table->string('user_nickname', 100);
            $table->string('blockingUser', 100);

            // foreign keys pointing to users.nickname
            $table->foreign('user_nickname')
                ->references('nickname')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('blockingUser')
                ->references('nickname')
                ->on('users')
                ->onDelete('cascade');

            $table->tinyInteger('status')->default(0);
            $table->timestamps();

            // optional - prevent duplicate block rows
            $table->unique(['user_nickname', 'blockingUser']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
