<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id')->unsigned();
            $table->dateTime('scheduled_from');
            $table->dateTime('scheduled_to');
            $table->integer('for_department')->nullable();
            $table->integer('for_doctor')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('cancelled')->default(false);
            $table->text('cancel_reason')->nullable();
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patients')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('appointment_prescriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('appointment_id')->unsigned();
            $table->integer('drug_id')->unsigned();
            $table->string('drug_name');
            $table->string('dosage');
            $table->string('frequency');
            $table->string('intake');
            $table->string('duration');
            $table->string('duration_type');
            $table->text('instruction')->nullable();
            $table->timestamps();

            $table->foreign('appointment_id')->references('id')->on('appointments')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('appointment_lab_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('appointment_id')->unsigned();
            $table->integer('lab_test_id')->unsigned();
            $table->string('lab_test_name');
            $table->text('instruction')->nullable();
            $table->timestamps();

            $table->foreign('appointment_id')->references('id')->on('appointments')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('appointment_clinical_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('appointment_id')->unsigned();
            $table->text('complaints')->nullable();
            $table->text('notes')->nullable();
            $table->text('observations')->nullable();
            $table->text('diagnoses')->nullable();
            $table->timestamps();

            $table->foreign('appointment_id')->references('id')->on('appointments')
                ->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::create('appointment_treatment_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('appointment_id')->unsigned();
            $table->integer('procedure_id')->unsigned();
            $table->string('procedure_name');
            $table->integer('procedure_units')->unsigned();
            $table->float('procedure_cost',8, 2);
            $table->float('procedure_discount', 8, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('appointment_id')->references('id')->on('appointments')
                ->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::create('appointment_completed_procedures', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('appointment_id')->unsigned();
            $table->integer('procedure_id')->unsigned();
            $table->string('procedure_name');
            $table->integer('procedure_units')->unsigned();
            $table->float('procedure_cost',8, 2);
            $table->float('procedure_discount', 8, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('appointment_id')->references('id')->on('appointments')
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
        Schema::dropIfExists('appointment_completed_procedures');
        Schema::dropIfExists('appointment_treatment_plans');
        Schema::dropIfExists('appointment_clinical_notes');
        Schema::dropIfExists('appointment_lab_orders');
        Schema::dropIfExists('appointment_prescriptions');
        Schema::dropIfExists('appointments');
    }
}
