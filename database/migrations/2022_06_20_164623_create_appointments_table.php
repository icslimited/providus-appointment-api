<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('startTime');
            $table->time('endTime');
            $table->string('location');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('guestName');
            $table->string('guestEmail')->nullable();
            $table->integer('guestContact')->nullable();
            $table->string('guestOrganization')->nullable();
            $table->text('reason');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
    }
};
