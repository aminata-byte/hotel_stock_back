<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->string('subject');
            $table->string('recipient');
            $table->foreignId('user_id')->constrained();
        });
    }

    public function down()
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->dropColumn(['subject', 'recipient', 'user_id']);
        });
    }
};
