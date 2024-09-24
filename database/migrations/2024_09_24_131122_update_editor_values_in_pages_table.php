<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            
        // We set it to 'markdown' for pages currently with markdown content
        DB::table('pages')->where('markdown', '!=', '')->update(['editor' => 'markdown']);
        // We set it to 'wysiwyg' where we have HTML but no markdown
        DB::table('pages')->where('markdown', '=', '')
            ->where('html', '!=', '')
            ->update(['editor' => 'wysiwyg']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            //
        });
    }
};
