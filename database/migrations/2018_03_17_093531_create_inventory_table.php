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
            $table->string('item_details_id');
            $table->string('item_code')->nullable();
            $table->string('item_manufacturer')->nullable();
            $table->string('item_stocking_unit');
            $table->integer('item_reorder_level')->nullable();
            $table->float('item_retail_price')->nullable();
            $table->string('item_type');
            $table->dateTime('deleted_at')->nullable();
            $table->integer('updated_by_user')->unsigned();
            $table->integer('created_by_user')->unsigned();
            $table->timestamps();

            $table->foreign('updated_by_user')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('created_by_user')->references('id')->on('users')
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
        Schema::dropIfExists('inventory');
    }
}
