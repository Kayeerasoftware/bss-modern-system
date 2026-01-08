<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->string('sender_id');
            $table->string('receiver_id');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->string('attachment')->nullable();
            $table->timestamps();
            
            $table->foreign('sender_id')->references('member_id')->on('members')->onDelete('cascade');
            $table->foreign('receiver_id')->references('member_id')->on('members')->onDelete('cascade');
            $table->index(['sender_id', 'receiver_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
};
