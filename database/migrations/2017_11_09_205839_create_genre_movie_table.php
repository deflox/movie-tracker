<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGenreMovieTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('genre_movie', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('genre_id')->unsigned();
            $table->integer('movie_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('genre_movie', function (Blueprint $table) {
            $table->foreign('genre_id')->references('id')->on('genres')->onDelete('cascade');
            $table->foreign('movie_id')->references('id')->on('movies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('genre_movie', function (Blueprint $table) {
            $table->dropForeign('genre_movie_genre_id_foreign');
            $table->dropForeign('genre_movie_movie_id_foreign');
        });

        Schema::dropIfExists('genre_movie');
    }
}
