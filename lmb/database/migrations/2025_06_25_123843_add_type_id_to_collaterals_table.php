<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->unsignedBigInteger('type_id')->nullable()->after('collateral_id');
            // Optional: Add foreign key constraint if `collateral_types` table exists
            // $table->foreign('type_id')->references('id')->on('collateral_types')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('collaterals', function (Blueprint $table) {
            // Drop foreign key first if it exists
            // $table->dropForeign(['type_id']);
            $table->dropColumn('type_id');
        });
    }
};