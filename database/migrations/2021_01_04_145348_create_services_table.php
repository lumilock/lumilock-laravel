<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name',150)->nullable(false);
            $table->string('uri',190)->unique()->nullable(false);
            $table->string('secret',100)->nullable(false);
            $table->string('path',190)->unique()->nullable(false);
            $table->string('picture_512',255)->default('');
            $table->string('picture_256',255)->default('');
            $table->string('picture_128',255)->default('');
            $table->string('picture_64',255)->default('');
            $table->string('picture_32',255)->default('');
            $table->string('picture_16',255)->default('');
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
        Schema::dropIfExists('services');
    }
}
