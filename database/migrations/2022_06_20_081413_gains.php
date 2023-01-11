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
        Schema::create('gains', function (Blueprint $table) {
            $table->id();
            $table->float('amount')->default(0.00);
            $table->string('nameGroup');
            $table->date('date');
            $table->integer('nbPersonnes');
            $table->float('gainIndividuel');
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
        Schema::dropIfExists('gains');
    }
};
