<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantPosTables extends Migration
{
    public function up()
    {
        // Suppliers Table
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_info'); // email, phone
            $table->string('address');
            $table->timestamps();
        });

        // Stock Table
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('quantity');
            $table->string('unit');
            $table->decimal('cost_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->date('expiry_date')->nullable();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->string('batch_number')->nullable();
            $table->json('images')->nullable(); // Store multiple image paths in JSON format
            $table->timestamps();
        });

        // Sales Table
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('item_name'); // Generic item name linked to stock
            $table->foreignId('stock_id')->constrained()->onDelete('cascade'); // Link to stock item
            $table->integer('quantity_sold');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('profit', 10, 2);
            $table->boolean('is_returned')->default(false);
            $table->timestamps();
        });

        // Purchase Orders Table
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->date('order_date');
            $table->enum('status', ['Pending', 'Completed']);
            $table->decimal('total_cost', 10, 2);
            $table->timestamps();
        });

        // Purchase Order Items Table
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('stock_id')->constrained()->onDelete('cascade');
            $table->integer('quantity_ordered');
            $table->integer('received_quantity')->nullable();
            $table->decimal('unit_cost', 10, 2);
            $table->timestamps();
        });

        // Waste Management Table
        Schema::create('waste_management', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained()->onDelete('cascade');
            $table->integer('quantity_wasted');
            $table->string('reason');
            $table->date('date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waste_management');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('stocks');
        Schema::dropIfExists('suppliers');
    }
}
