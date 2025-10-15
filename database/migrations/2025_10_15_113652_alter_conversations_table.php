<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // unique index that guarantees only one two-person conversation
        DB::statement('
            ALTER TABLE conversation_user
              ADD COLUMN user_least INT UNSIGNED AS (LEAST(conversation_id, user_id)) VIRTUAL,
              ADD COLUMN user_greatest INT UNSIGNED AS (GREATEST(conversation_id, user_id)) VIRTUAL,
              ADD UNIQUE INDEX two_person_conv (user_least, user_greatest)
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE conversation_user DROP INDEX two_person_conv, DROP COLUMN user_least, DROP COLUMN user_greatest');
    }
};
