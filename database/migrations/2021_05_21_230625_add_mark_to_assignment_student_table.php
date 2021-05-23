<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMarkToAssignmentStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assignment_student', function (Blueprint $table) {
            $table->unsignedBigInteger('mark')->after('haded_date')->nullable(); //  FUCKING TYPO IN TABLE NAME!!!
            $table->text('feedback')->after('mark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assignment_student', function (Blueprint $table) {
            $table->dropColumn('mark');
            $table->dropColumn('feedback');
        });
    }
}
