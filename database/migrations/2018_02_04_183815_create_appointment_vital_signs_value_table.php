<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppointmentVitalSignsValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointment_vital_signs_value', function (Blueprint $table) {
            $table->integer('appointment_vital_signs_id')->unsigned();
            $table->string('name');
            $table->string('unit')->nullable();
            $table->text('value');
            $table->timestamps();
            $table->foreign('appointment_vital_signs_id')->references('id')->on('appointment_vital_signs')
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
        Schema::dropIfExists('appointment_vital_signs_value');
    }
}
