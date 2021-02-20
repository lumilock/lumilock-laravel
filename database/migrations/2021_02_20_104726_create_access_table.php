<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access', function (Blueprint $table) {
            $table->id();
            $table->uuid('permission_id')->nullable(false);
            $table->uuid('api_key_id')->nullable(false);

            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('api_key_id')
                ->references('id')
                ->on('api_keys')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('access', function (Blueprint $table) {
            $table->dropForeign('access_permission_id_foreign');
            $table->dropForeign('access_api_key_id_foreign');
        });
        Schema::dropIfExists('access');
    }
}
