<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionImageToConsultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consults', function (Blueprint $table) {
            //
            $table->renameColumn("consulting", 'name') ;
            $table->string("image")->nullable()->after('consulting');
            $table->text("description")->after("consulting");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consults', function (Blueprint $table) {
            //
            $table->renameColumn("name", 'consulting');
            $table->dropColumn('description');
            $table->dropColumn("image");
        });
    }
}
