<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('mobile', 10)->nullable();
            $table->string('email')->nullable();
            $table->char('gender')->nullable();
            $table->date('dob')->nullable();
            $table->decimal('age')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('occupation')->nullable();
            $table->text('street_address')->nullable();
            $table->string('pincode', 6)->nullable();
            $table->string('city')->nullable();
            $table->timestamps();
        });

        Schema::create('patients_medical_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id')->unsigned();
            $table->text('description')->nullable();
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
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
        Schema::dropIfExists('patients_medical_history');
        Schema::dropIfExists('patients');
    }
}
