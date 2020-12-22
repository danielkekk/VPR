<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocalmediaPosztTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('localmedia_poszt', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('media_id');
            $table->string('ev',10);
            $table->string('honap',10);
            $table->string('nap',10);
            $table->integer('kovetok_szama')->nullable();
            $table->longText('posztok')->nullable();
            $table->integer('stat_poszt_sum')->nullable();
            $table->integer('stat_reakciok_sum')->nullable();
            $table->integer('stat_altalanos_sum')->nullable();
            $table->integer('stat_alpolg_sum')->nullable();
            $table->integer('stat_polg_sum')->nullable();
            $table->integer('stat_privat_sum')->nullable();
            $table->integer('stat_ogykepviselo_sum')->nullable();
            $table->double('stat_atlag_hm')->nullable();
            $table->timestamp('datum');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('localmedia_poszt');
    }
}
