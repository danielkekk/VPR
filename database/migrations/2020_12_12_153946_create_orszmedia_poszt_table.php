<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrszmediaPosztTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orszmedia_poszt', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('media_id');
            $table->string('ev',10);
            $table->string('honap',10);
            $table->string('nap',10);
            $table->integer('kovetok_szama')->nullable();
            $table->longText('posztok')->nullable();
            $table->integer('stat_poszt_sum')->nullable();
            $table->integer('stat_reakciok_sum')->nullable();
            $table->integer('stat_sajat_sum')->nullable();
            $table->integer('stat_szemelyes_sum')->nullable();
            $table->integer('stat_polgarmesteri_sum')->nullable();
            $table->integer('stat_alpolgarmesteri_sum')->nullable();
            $table->integer('stat_csoportoldal_sum')->nullable();
            $table->integer('stat_media_sum')->nullable();
            $table->integer('stat_kepviselotars_sum')->nullable();
            $table->integer('stat_egyeb_sum')->nullable();
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
        Schema::dropIfExists('orszmedia_poszt');
    }
}
