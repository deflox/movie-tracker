<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMoviesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_movies', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('watched');
            $table->integer('movie_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('user_movies', function (Blueprint $table) {
            $table->foreign('movie_id')->references('id')->on('movies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_movies', function (Blueprint $table) {
            $table->dropForeign('user_movies_movie_id_foreign');
            $table->dropForeign('user_movies_user_id_foreign');
        });

        Schema::dropIfExists('user_movies');
    }
}
