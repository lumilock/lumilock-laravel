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
            $table->string('name',150)->nullable(false); // exemple : Audit Lait Cru
            $table->string('uri',190)->unique()->nullable(false); // exemple : http://localhost:8002
            $table->string('secret',100)->nullable(false); // exemple : 123456789
            $table->string('path',190)->unique()->nullable(false); // exemple /api/auditLaitCru
            $table->string('address',190)->unique()->nullable(false); // exemple http://localhost:3001/whatever
            $table->string('picture',2000)->default(''); // exemple : https://i.pinimg.com/originals/8f/c3/7b/8fc37b74b608a622588fbaa361485f32.png
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
