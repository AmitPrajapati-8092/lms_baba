<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CourseCode extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('course_code', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('org_id')->default(0);
            $table->string('code_name', 255)->nullable($value = true);
            $table->string('code', 255)->nullable($value = true);
            $table->string('code_status', 100)->default('Inactive');
            $table->ipAddress('ip_address')->nullable();
            $table->integer('is_deleted')->default(0)->comment('0=not deleted,1=is_deleted');
            $table->integer('deleted_by')->default(0);
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('course_code');
    }

}
