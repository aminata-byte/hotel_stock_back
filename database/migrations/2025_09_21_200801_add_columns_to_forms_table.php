<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->string('title');
            $table->foreignId('user_id')->constrained();
        });
    }

    public function down()
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn(['title', 'user_id']);
        });
    }
};
