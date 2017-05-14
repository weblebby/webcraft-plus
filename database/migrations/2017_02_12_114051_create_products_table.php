<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('server_id')->unsigned();
            $table->integer('day')->nullable();
            $table->string('name');
            $table->string('description')->nullable();
            $table->text('commands');
            $table->string('prefix')->nullable();
            $table->string('icon');
            $table->double('price', 64, 2)->nullable();
            $table->string('type');
            $table->boolean('active')->default(1);
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('server_id')
                ->references('id')->on('servers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
