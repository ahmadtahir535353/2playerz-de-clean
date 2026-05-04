<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPsnAndXboxLiveIdsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('psn_id')->nullable()->after('email'); // PSN ID ke liye column, optional
            $table->string('xbox_live_id')->nullable()->after('psn_id'); // Xbox Live ID ke liye column, optional
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('psn_id');
            $table->dropColumn('xbox_live_id');
        });
    }
}