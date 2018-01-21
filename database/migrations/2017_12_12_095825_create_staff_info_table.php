<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_information', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->char('gender')->nullable();
            $table->date('dob')->nullable();
            $table->string('blood_group')->nullable();
            $table->text('street_address')->nullable();
            $table->string('pincode', 6)->nullable();
            $table->string('city')->nullable();
            $table->string('registration_number')->nullable();
            $table->timestamps();
            $table->primary(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')
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
        Schema::drop('staff_information');
    }
}
