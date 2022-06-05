<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePrescriptionIdInEyeDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eye_details', function (Blueprint $table) {
            $table->renameColumn('prescription_id', 'appointment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('eye_details', function (Blueprint $table) {
            $table->renameColumn('appointment_id', 'prescription_id');
        });
    }
}
