<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientPaymentHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_payment_history', function (Blueprint $table) {
            $table->id();
            $table->integer('patient_id');
            $table->string('hospitals_id');
            $table->string('device_id');
            $table->string('card_brand');
            $table->string('card_id');
            $table->string('last4_digit');
            $table->string('expiry_year');
            $table->string('expiry_month');
            $table->json('response');
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
        Schema::dropIfExists('patient_payment_history');
    }
}
