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
        Schema::table('client_order_summary', function (Blueprint $table) {
            $table->integer('recency')->default(0);
            $table->integer('frequency')->default(0);
            $table->integer('monetary')->default(0);
            $table->float('rfm')->virtualAs('ROUND((recency + frequency + monetary) / 3, 2)')->index();

            $table->index(['recency', 'frequency', 'monetary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_order_summary', function (Blueprint $table) {
            $table->dropIndex(['recency', 'frequency', 'monetary']);
            $table->dropColumn(['recency', 'frequency', 'monetary', 'rfm']);
        });
    }
};
