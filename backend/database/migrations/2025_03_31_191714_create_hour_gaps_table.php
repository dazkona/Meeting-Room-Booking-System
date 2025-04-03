<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Room;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hour_gaps', function (Blueprint $table) {
            $table->id();
			$table->foreignIdFor(Room::class)->constrained();
			$table->string('user_name')->default("");
			$table->date('date');
			$table->integer('hour'); // 9 means from 9:00 to 9:59
			$table->boolean('booked')->default(0);
            $table->timestamps();

			$table->unique(['room_id', 'date', 'hour']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hour_gaps');
    }
};
