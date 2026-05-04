<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEditorAndModeratorRolesToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_editor')->default(false)->after('email'); // Editor role ke liye column
            $table->boolean('is_moderator')->default(false)->after('is_editor'); // Moderator role ke liye column
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_editor');
            $table->dropColumn('is_moderator');
        });
    }
}