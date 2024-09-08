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
        Schema::create('follows', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('following')->unsigned();
            $table->unsignedInteger('followed')->unsigned();
            $table->timestamps();

            $table->foreign('following')->references('id')->on('users');
            $table->foreign('followed')->references('id')->on('users');

            $table->unique(['following', 'followed']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('follows');
    }
};
