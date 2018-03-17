<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_stock', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('inventory_id')->unsigned();
            $table->integer('quantity');
            $table->string('batch_number')->nullable();
            $table->date('expiry')->nullable();
            $table->float('unit_cost')->nullable();
            $table->timestamps();

            $table->foreign('inventory_id')->references('id')->on('inventory')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_stock');
    }
}
