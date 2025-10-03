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
        Schema::create('client_order_summary', function (Blueprint $table) {
            $table->integer('client_id')->primary()->unique()->index();
            $table->string('name')->notNullable();
            $table->integer('number_of_orders')->default(0);
            $table->integer('total_price_of_orders')->default(0);
            $table->date('most_recent_order_date')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_order_summary');
    }
};
