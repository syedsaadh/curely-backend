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
        Schema::create('inventory_stock_add', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('inventory_id')->unsigned();
            $table->integer('quantity');
            $table->string('batch_number')->nullable();
            $table->date('expiry')->nullable();
            $table->float('unit_cost')->nullable();
            $table->dateTime('added_on')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->integer('updated_by_user')->unsigned();
            $table->integer('created_by_user')->unsigned();
            $table->timestamps();

            $table->foreign('updated_by_user')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('created_by_user')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('inventory_id')->references('id')->on('inventory')
                ->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::create('inventory_stock_consume', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('inventory_stock_add_id')->unsigned();
            $table->integer('quantity');
            $table->string('consume_type');
            $table->integer('consumed_by_patient_id')->unsigned();
            $table->dateTime('consumed_on')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->integer('updated_by_user')->unsigned();
            $table->integer('created_by_user')->unsigned();

            $table->timestamps();

            $table->foreign('updated_by_user')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('created_by_user')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('inventory_stock_add_id')->references('id')->on('inventory_stock_add')
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
        Schema::dropIfExists('inventory_stock_consume');
        Schema::dropIfExists('inventory_stock_add');
    }
}
