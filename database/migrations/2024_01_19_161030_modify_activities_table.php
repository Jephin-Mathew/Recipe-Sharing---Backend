<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        {
            Schema::table('activities', function (Blueprint $table) {
                // Remove 'activity_type' and 'description' columns
                $table->dropColumn('activity_type');
                $table->dropColumn('description');
                $table->dropColumn('related_id');
        
                // Rename 'related_id' to 'recipe_id'
                $table->unsignedBigInteger('recipe_id')->after('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            // Reverse the changes made in the 'up' method
            $table->enum('activity_type', ['recipe_created', 'recipe_updated', 'recipe_deleted', 'user_followed', 'recipe_liked']);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('related_id')->after('user_id');
            $table->dropColumn('recipe_id');
            // Rename 'recipe_id' back to 'related_id'
            // $table->renameColumn('recipe_id', 'related_id');
        });
    }
};
