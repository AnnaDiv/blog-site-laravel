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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('user_nickname', 100);
            $table->foreign('user_nickname')->references('nickname')->on('users')->onDelete('cascade');
            $table->string('title', 255);
            $table->text('content');
            $table->string('image_folder', 255);
            $table->integer('likes')->default(0);
            $table->integer('comments')->default(0);
            $table->timestamp('time')->useCurrent();
            $table->string('status', 10)->default('public');
            $table->tinyInteger('deleted')->default(0);
            $table->string('type', 10)->default('post');
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100)->unique();
            $table->string('description', 255);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
        Schema::dropIfExists('categories');
    }
};
