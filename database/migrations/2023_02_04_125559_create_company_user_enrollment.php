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
        Schema::create('company_user_enrollment', function (Blueprint $table) {
                $table->unsignedBigInteger('id_company');
                $table->unsignedBigInteger('id_user');

                $table->foreign('id_company')
                    ->references('id')
                    ->on('company');
                $table->foreign('id_user')
                    ->references('id')
                    ->on('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_user_enrollment');
    }
};
