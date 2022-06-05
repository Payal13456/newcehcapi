<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient', function (Blueprint $table) {
            $table->id();
            $table->string('parent_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email_address');
            $table->string('password');
            $table->string('phone_number_primary');
            $table->string('phone_number_secondary');
            $table->string('date_of_birth');
            $table->string('blood_group');
            $table->string('gender');
            $table->string('address');
            $table->string('city');
            $table->string('pincode');
            $table->string('adhar_card');
            $table->string('type_of_patient');
            $table->string('upload_of_picture');
            $table->string('ip_address');
            $table->tinyInteger('status')->default(1)->comment("0: in active, 1: active, 2: deleted, 3: suspended");
            $table->softDeletes();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patient');
    }
}
