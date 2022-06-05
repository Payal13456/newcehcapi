<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersInfomationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_infomation', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('specialization_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('month_dob')->nullable();
            $table->string('phonenumber')->nullable();
            $table->string('aadharnumber')->nullable();
            $table->string('gender')->nullable();
            $table->string('picture')->nullable();
            $table->string('education_qulaification')->nullable();
            $table->string('description')->nullable();
            $table->string('address')->nullable();
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
        Schema::dropIfExists('users_infomation');
    }
}
