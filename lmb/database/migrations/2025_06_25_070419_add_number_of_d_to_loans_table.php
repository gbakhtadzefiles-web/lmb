<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->integer('number_of_d')->default(1)->after('interest_rate');
        });

        // Optional: update existing rows with value 1 explicitly
        \Illuminate\Support\Facades\DB::table('loans')->update(['number_of_d' => 1]);
    }

    public function down()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn('number_of_d');
        });
    }
};