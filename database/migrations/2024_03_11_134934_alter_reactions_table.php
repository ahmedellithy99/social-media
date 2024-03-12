<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reactions', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
            $table->renameColumn('post_id', 'reactable_id');
        });
        Schema::table('reactions', function (Blueprint $table) {
            $table->string('reactable_type')->after('reactable_id');
            
        });

        DB::table('reactions')
            ->update(['reactable_type' => 'App\Models\Post']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reactions', function (Blueprint $table) {
            
        });
        Schema::table('reactions', function (Blueprint $table) {
            $table->dropColumn('reactable_type');
            $table->renameColumn('reactable_id', 'post_id');
            $table->foreign('post_id')->references('id')->on('posts');
        });
    }
};