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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 8, 2); // Retail price per unit, for reference
            $table->text('description')->nullable();
            $table->unsignedBigInteger('category_id')->nullable(); // Add category_id as a foreign key
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null'); // Add foreign key constraint
            $table->timestamps();
        });

        // Stock Table
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity'); // e.g., number of packets or units
            $table->decimal('price_per_unit', 8, 2); // Cost per unit
            $table->decimal('output_per_unit', 8, 2); // Expected output (e.g., servings per packet)
            $table->integer('available_servings')->default(0); // Auto-calculated based on usage
            $table->timestamps();
        });

        // Sales Table
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('stock_id')->constrained()->onDelete('cascade');
            $table->integer('quantity'); // Quantity sold
            $table->decimal('price_per_unit', 8, 2); // Price per unit at sale time
            $table->decimal('total_price', 10, 2); // Auto-calculated as quantity * price_per_unit
            $table->timestamps();
        });

        Schema::create('excess_demand', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('sale_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('requested_quantity'); // Requested quantity that exceeded stock
            $table->integer('available_servings'); // Available stock at the time of request
            $table->integer('excess_quantity'); // Difference between requested and available stock
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
        Schema::dropIfExists('excess_demand');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('stocks');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('suppliers');
    }
}
