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
        DB::table('rooms')->insert([[
            'name' => "Room One"
        ],[
            'name' => "Room Two"
        ],[
            'name' => "Room Three"
        ],[
            'name' => "Room Four"
        ],[
            'name' => "Room Five"
        ]]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
		DB::table('rooms')->whereIn('name', ["Room One", "Room Two", "Room Three", "Room Four", "Room Five"])->delete();
    }
};
