<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('name');
            $table->string('email');
            $table->string('address');
            $table->string('remarks')->nullable();
            $table->json('products');
            $table->enum('order_status', ['draft', 'confirmed', 'cancelled']);
            $table->enum('tracking_status', ['pending', 'label_generated', 'shipped', 'delivered']);
            $table->enum('payment_status', ['due', 'paid']);
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
        Schema::dropIfExists('orders');
    }
}
