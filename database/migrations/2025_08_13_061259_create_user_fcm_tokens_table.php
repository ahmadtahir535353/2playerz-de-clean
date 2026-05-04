// database/migrations/xxxx_xx_xx_create_user_fcm_tokens_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_fcm_tokens', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('token', 191)->unique(); 
            $t->string('device')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('user_fcm_tokens'); }
};
