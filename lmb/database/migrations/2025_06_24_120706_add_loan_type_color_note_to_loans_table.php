<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('loans', function (Blueprint $table) {
        $table->integer('loan_type')->nullable()->after('loan_id');   // or after any existing column
        $table->integer('loan_color')->nullable()->after('loan_type');
        $table->text('note')->nullable()->after('loan_color');
    });
}

public function down()
{
    Schema::table('loans', function (Blueprint $table) {
        $table->dropColumn(['loan_type', 'loan_color', 'note']);
    });
}
};
