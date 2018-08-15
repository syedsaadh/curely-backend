<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIpdRelatedTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ipd_admission', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id')->unsigned();
            $table->dateTime('admitted_on');
            $table->dateTime('discharged_on')->nullable();
            $table->integer('in_department')->nullable();
            $table->integer('referred_by_doctor')->nullable();
            $table->integer('bed_no');
            $table->text('notes')->nullable();
            $table->boolean('soft_delete')->default(false);
            $table->integer('updated_by_user')->unsigned();
            $table->timestamps();

            $table->foreign('updated_by_user')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')
                ->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::create('ipd_admission_visit', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ipd_admission_id')->unsigned();
            $table->string('visit_type');
            $table->string('visited_by')->nullable();
            $table->dateTime('visited_on')->nullable();
            $table->integer('created_by_user_id')->unsigned();
            $table->integer('updated_by_user')->unsigned();
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();

            $table->foreign('updated_by_user')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('created_by_user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::create('ipd_prescriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ipd_admission_visit_id')->unsigned();
            $table->integer('drug_id')->unsigned();
            $table->string('intake');
            $table->string('frequency');
            $table->string('display_frequency')->nullable();
            $table->string('food_precedence');
            $table->integer('duration');
            $table->string('duration_unit');
            $table->integer('morning_units')->nullable();
            $table->integer('afternoon_units')->nullable();
            $table->integer('night_units')->nullable();
            $table->text('instruction')->nullable();
            $table->integer('created_by_user_id')->unsigned();
            $table->integer('updated_by_user_id')->unsigned();
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();

            $table->foreign('created_by_user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('updated_by_user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('ipd_admission_visit_id')->references('id')->on('ipd_admission_visit')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('ipd_lab_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ipd_admission_visit_id')->unsigned();
            $table->integer('lab_test_id')->unsigned();
            $table->string('lab_test_name');
            $table->text('instruction')->nullable();
            $table->integer('updated_by_user')->unsigned();
            $table->timestamps();

            $table->foreign('updated_by_user')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('ipd_admission_visit_id')->references('id')->on('ipd_admission_visit')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('ipd_clinical_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ipd_admission_visit_id')->unsigned();
            $table->text('complaints')->nullable();
            $table->text('notes')->nullable();
            $table->text('observations')->nullable();
            $table->text('diagnosis')->nullable();
            $table->integer('updated_by_user')->unsigned();
            $table->timestamps();

            $table->foreign('updated_by_user')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('ipd_admission_visit_id')->references('id')->on('ipd_admission_visit')
                ->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::create('ipd_treatment_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ipd_admission_visit_id')->unsigned();
            $table->integer('procedure_id')->unsigned();
            $table->string('procedure_name');
            $table->integer('procedure_units')->unsigned();
            $table->float('procedure_cost',8, 2);
            $table->float('procedure_discount', 8, 2);
            $table->text('notes')->nullable();
            $table->integer('updated_by_user')->unsigned();
            $table->timestamps();

            $table->foreign('updated_by_user')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('ipd_admission_visit_id')->references('id')->on('ipd_admission_visit')
                ->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::create('ipd_completed_procedures', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ipd_admission_visit_id')->unsigned();
            $table->integer('procedure_id')->unsigned();
            $table->string('procedure_name');
            $table->integer('procedure_units')->unsigned();
            $table->float('procedure_cost',8, 2);
            $table->float('procedure_discount', 8, 2);
            $table->text('notes')->nullable();
            $table->integer('updated_by_user')->unsigned();
            $table->timestamps();

            $table->foreign('updated_by_user')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('ipd_admission_visit_id')->references('id')->on('ipd_admission_visit')
                ->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::create('ipd_vital_signs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ipd_admission_visit_id')->unsigned();
            $table->integer('updated_by_user')->unsigned();
            $table->timestamps();

            $table->foreign('updated_by_user')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('ipd_admission_visit_id')->references('id')->on('ipd_admission_visit')
                ->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::create('ipd_vital_signs_value', function (Blueprint $table) {
            $table->integer('ipd_vital_signs_id')->unsigned();
            $table->string('name');
            $table->string('unit')->nullable();
            $table->text('value');
            $table->timestamps();
            $table->foreign('ipd_vital_signs_id')->references('id')->on('ipd_vital_signs')
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
        Schema::dropIfExists('ipd_vital_signs_value');
        Schema::dropIfExists('ipd_vital_signs');
        Schema::dropIfExists('ipd_completed_procedures');
        Schema::dropIfExists('ipd_treatment_plans');
        Schema::dropIfExists('ipd_clinical_notes');
        Schema::dropIfExists('ipd_lab_orders');
        Schema::dropIfExists('ipd_prescriptions');
        Schema::dropIfExists('ipd_admission_visit');
        Schema::dropIfExists('ipd_admission');
    }
}
