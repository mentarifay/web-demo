<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gas_volume', function (Blueprint $table) {
            $table->id();
            $table->string('shipper');
            $table->integer('tahun');
            $table->integer('bulan');
            $table->string('periode'); // Jan-20, Feb-20, dst
            $table->decimal('daily_average_mmscfd', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gas_volume');
    }
};