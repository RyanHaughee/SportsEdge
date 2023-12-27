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
        Schema::create('nfl_kick_stats', function (Blueprint $table) {
            $table->id();
            $table->integer('season');
            $table->integer('week');
            $table->string('season_type')->nullable();
            $table->string('team');
            $table->string('player_name')->nullable();
            $table->string('player_id');
            $table->integer('fg_made')->nullable();
            $table->integer('fg_missed')->nullable();
            $table->integer('fg_blocked')->nullable();         
            $table->integer('fg_long')->nullable();
            $table->integer('fg_att')->nullable();
            $table->float('fg_pct')->nullable();             
            $table->integer('pat_made')->nullable();
            $table->integer('pat_missed')->nullable();
            $table->integer('pat_blocked')->nullable();        
            $table->integer('pat_att')->nullable();
            $table->float('pat_pct')->nullable();
            $table->integer('fg_made_distance')->nullable();   
            $table->integer('fg_missed_distance')->nullable();
            $table->integer('fg_blocked_distance')->nullable();
            $table->integer('gwfg_att')->nullable();           
            $table->integer('gwfg_distance')->nullable();
            $table->integer('gwfg_made')->nullable();
            $table->integer('gwfg_missed')->nullable();        
            $table->integer('gwfg_blocked')->nullable();
            $table->integer('fg_made_0_19')->nullable();
            $table->integer('fg_made_20_29')->nullable();      
            $table->integer('fg_made_30_39')->nullable();
            $table->integer('fg_made_40_49')->nullable();
            $table->integer('fg_made_50_59')->nullable();      
            $table->integer('fg_made_60_')->nullable();
            $table->integer('fg_missed_0_19')->nullable();
            $table->integer('fg_missed_20_29')->nullable();    
            $table->integer('fg_missed_30_39')->nullable();
            $table->integer('fg_missed_40_49')->nullable();
            $table->integer('fg_missed_50_59')->nullable();    
            $table->integer('fg_missed_60_')->nullable();
            $table->string('fg_made_list')->nullable();
            $table->string('fg_missed_list')->nullable();     
            $table->string('fg_blocked_list')->nullable(); 
            // Add more columns as needed
            $table->timestamps();
        });

        // Create table indexes
        Schema::table('nfl_kick_stats', function ($table) {
            $table->index('player_id', 'nfl_kick_stats_player_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nfl_kick_stats');
    }
};
