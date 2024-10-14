<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeImagesFieldInStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            // Drop the JSON column
            $table->dropColumn('images');
            // Add a new string column for the file path
            $table->string('image')->nullable(); // Updated field
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stocks', function (Blueprint $table) {
            // Drop the new column
            $table->dropColumn('image');
            // Recreate the original JSON column
            $table->json('images')->nullable(); // Original field
        });
    }
}
