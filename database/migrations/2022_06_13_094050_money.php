<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('money', function (Blueprint $table) {
        $table->id();
        $table->string('pseudo');
        $table->bigInteger('id_pseudo');
        $table->float('amount')->default(0.00);
        $table->float('credit')->default(0.00);
        $table->float('debit')->default(0.00);
        $table->float('creditGain')->default(0.00);
        $table->timestamp('date')->default(DB::raw('CURRENT_TIMESTAMP'));
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('money');
    }
};
