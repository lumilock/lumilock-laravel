<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name',150)->nullable(false);
            $table->string('description',500)->nullable(true);
            $table->string('uri',190)->unique()->nullable(false);
            $table->string('token',100)->nullable(false);
            $table->date('expires_at')->nullable(false);
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
        Schema::dropIfExists('api_keys');
    }
}
