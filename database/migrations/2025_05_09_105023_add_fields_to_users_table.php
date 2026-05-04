<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Avatar (store the file path as a string, nullable since it's optional)
            $table->string('avatar')->nullable()->after('password');

            // About Me (text area, nullable)
            $table->text('about_me')->nullable()->after('avatar');

            // Location (string, nullable)
            $table->string('location')->nullable()->after('about_me');

            // Occupation (string, nullable)
            $table->string('occupation')->nullable()->after('location');

            // Interests (text area, nullable)
            $table->text('consoles')->nullable()->after('occupation');

            // Favorite Games (text area, nullable)
            $table->text('favorite_games')->nullable()->after('consoles');

            // Favorite Genre (text area, nullable)
            $table->text('favorite_genre')->nullable()->after('favorite_games');

            // Favorite Series (text area, nullable)
            $table->text('favorite_series')->nullable()->after('favorite_genre');

            // Favorite Films (text area, nullable)
            $table->text('favorite_films')->nullable()->after('favorite_series');

            // Favorite Music (text area, nullable)
            $table->text('favorite_music')->nullable()->after('favorite_films');

            // Hobbies (text area, nullable)
            $table->text('hobbies')->nullable()->after('favorite_music');

            // My Motto (text area, nullable)
            $table->text('my_motto')->nullable()->after('hobbies');

            $table->enum('theme', ['light', 'dark'])->default('light')->after('my_motto');

            $table->timestamp('last_activity_at')->nullable()->after('theme');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the columns in reverse order if rolling back
            $table->dropColumn([
                'avatar',
                'about_me',
                'location',
                'occupation',
                'interests',
                'favorite_games',
                'favorite_genre',
                'favorite_series',
                'favorite_films',
                'favorite_music',
                'hobbies',
                'my_motto',
                'theme'
            ]);
        });
    }
}
