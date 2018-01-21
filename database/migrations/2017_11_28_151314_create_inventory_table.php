<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->increments('id');
            $table->string('item_name');
            $table->string('item_code');
            $table->string('manufacturer');
            $table->string('item_type');
            $table->integer('reorder_level')->default(0);
            $table->float('retail_price', 8, 2);
            $table->timestamps();
        });
        Schema::create('inventory_stock', function (Blueprint $table) {
            $table->integer('item_id')->unsigned();
            $table->integer('quantity');
            $table->string('batch_number')->nullable();
            $table->float('unit_cost', 8, 2);

            $table->foreign('item_id')->references('id')->on('inventory')
                ->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::create('inventory_stock_add_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('item_name');
            $table->string('item_code');
            $table->string('manufacturer');
            $table->string('item_type');
            $table->integer('quantity');
            $table->string('batch_number')->nullable();
            $table->float('unit_cost', 8, 2);
            $table->float('retail_price', 8, 2);
            $table->dateTime('add_date_time');
        });
        Schema::create('inventory_stock_consume_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('item_name');
            $table->string('item_code');
            $table->string('manufacturer');
            $table->string('item_type');
            $table->integer('quantity');
            $table->string('batch_number')->nullable();
            $table->float('unit_cost', 8, 2);
            $table->float('retail_price', 8, 2);
            $table->dateTime('consume_date_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_stock_consume_logs');
        Schema::dropIfExists('inventory_stock_add_logs');
        Schema::dropIfExists('inventory_stock');
        Schema::dropIfExists('inventory');
    }
}
