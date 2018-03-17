<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryDrugCatalogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_drug_catalog', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('drug_type');
            $table->string('default_dosage')->nullable();
            $table->string('default_dosage_unit')->nullable();
            $table->text('instruction')->nullable();
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
        Schema::dropIfExists('inventory_drug_catalog');
    }
}
