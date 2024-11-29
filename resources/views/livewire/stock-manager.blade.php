<div class="container mx-auto p-6 bg-gray-50" x-data="{ showForm: false, showSaleForm: false, product_id: null, quantitySold: 0, salePrice: 0, totalAmount: 0 }">
    <h2 class="text-3xl font-semibold text-gray-800 mb-6 flex items-center">
        <i class="fas fa-cogs mr-3 text-blue-500"></i> Stock Management
    </h2>

    <!-- Toggle Form Button -->
    <button @click="showForm = !showForm" x-show="!showForm"
        class="px-4 py-2 bg-green-500 text-white rounded-lg shadow hover:bg-green-600 mb-6 focus:outline-none focus:ring-2 focus:ring-green-400">
        Add Stock
    </button>

    <!-- Stock Form -->
    <div class="mb-6 p-4 bg-gray-100 rounded-lg shadow-md" x-show="showForm" x-transition>
        <h3 class="text-2xl font-semibold text-gray-800 mb-4">{{ $editingStock ? 'Edit Stock' : 'Add Stock' }}</h3>
        <form wire:submit.prevent="{{ $editingStock ? 'updateStock' : 'addStock' }}" @submit="showForm = false">

            <!-- Grid Layout for Form -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- Product Selection -->
                <div class="mb-4 relative">
                    <label for="product_id" class="block text-gray-700 font-semibold">
                        Product
                        <i class="fas fa-info-circle text-gray-500 ml-1 cursor-pointer"
                            @click="showProductInfo = !showProductInfo"></i>
                    </label>
                    <p x-show="showProductInfo" class="text-xs text-gray-500 mt-1" x-transition>Choose the product for
                        stock management.</p>
                    <select wire:model.live="product_id" id="product_id"
                        class="p-2 border border-gray-300 rounded-lg w-full">
                        <option value="">Select Product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Quantity Input -->
                <div class="mb-4 relative">
                    <label for="newStockQuantity" class="block text-gray-700 font-semibold">
                        Quantity
                        <i class="fas fa-info-circle text-gray-500 ml-1 cursor-pointer"
                            @click="showQuantityInfo = !showQuantityInfo"></i>
                    </label>
                    <p x-show="showQuantityInfo" class="text-xs text-gray-500 mt-1" x-transition>Enter the stock
                        quantity available.</p>
                    <input type="number" wire:model.live="newStockQuantity" id="newStockQuantity"
                        class="p-2 border border-gray-300 rounded-lg w-full">
                    @error('newStockQuantity')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Price Per Unit -->
                <div class="mb-4 relative">
                    <label for="price_per_unit" class="block text-gray-700 font-semibold">
                        Price Per Unit
                        <!-- Badge to display the actual value dynamically -->
                        <span class="ml-2 inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            ${{ number_format($price_per_unit, 2) }}
                        </span>
                        <i class="fas fa-info-circle text-gray-500 ml-1 cursor-pointer" @click="showPriceInfo = !showPriceInfo"></i>
                    </label>
                    <p x-show="showPriceInfo" class="text-xs text-gray-500 mt-1" x-transition>
                        This is the cost per unit from the product.
                    </p>
                    <!-- Read-Only Price -->
                    <input type="number" step="0.01" wire:model.live="price_per_unit" id="price_per_unit"
                        class="p-2 border border-gray-300 rounded-lg w-full bg-gray-100 cursor-not-allowed" readonly>
                    @error('price_per_unit')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                
                
                

                <!-- Output Per Unit -->
                <div class="mb-4 relative">
                    <label for="output_per_unit" class="block text-gray-700 font-semibold">
                        Output Per Unit
                        <i class="fas fa-info-circle text-gray-500 ml-1 cursor-pointer"
                            @click="showOutputInfo = !showOutputInfo"></i>
                    </label>
                    <p x-show="showOutputInfo" class="text-xs text-gray-500 mt-1" x-transition>
                        Specify output or servings per unit.
                    </p>
                    <input type="number" wire:model.live="output_per_unit" id="output_per_unit"
                        class="p-2 border border-gray-300 rounded-lg w-full">
                    @error('output_per_unit')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Available Servings (Read-only) -->
                
                
                <div class="mb-4 relative">
                    <label for="available_servings" class="block text-gray-700 font-semibold">
                        Available Servings
                        <!-- Badge to display the actual value dynamically -->
                        <span class="ml-2 inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $available_servings }}
                        </span>
                        <i class="fas fa-info-circle text-gray-500 ml-1 cursor-pointer" @click="showServingsInfo = !showServingsInfo"></i>
                    </label>
                
                    <!-- Bootstrap Info Alert for Additional Information -->
                    <div x-show="showServingsInfo" class="alert alert-info text-sm mt-1" role="alert" x-transition>
                        Read-only field for remaining servings.
                    </div>
                
                    <input type="number" wire:model.live="available_servings" id="available_servings"
                        class="p-2 border border-gray-300 rounded-lg w-full bg-gray-100 cursor-not-allowed" readonly>
                </div>
                

            </div>

            <!-- Submit and Cancel Buttons -->
            <div class="flex items-center space-x-4 mt-4">
                <button type="submit"
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    {{ $editingStock ? 'Update Stock' : 'Add Stock' }}
                </button>
                <button type="button" wire:click="resetInputFields" @click="showForm = false"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg shadow hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400">
                    Cancel
                </button>
            </div>
        </form>
    </div>


    <!-- Stock Status Table -->
    <div class="mb-6 p-4 bg-white rounded-lg shadow-md" x-show="!showForm" wire:poll.1s>
        <h3 class="text-2xl font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-boxes mr-3 text-green-500"></i> Stock Status
        </h3>

        <!-- Date Range Filter -->
        <div class="flex space-x-4 mb-4">
            <div class="flex items-center space-x-2">
                <label for="start_date" class="text-sm text-gray-600">Start Date</label>
                <input type="date" id="start_date" wire:model.live="startDate" class="px-4 py-2 border rounded-lg">
            </div>
            <div class="flex items-center space-x-2">
                <label for="end_date" class="text-sm text-gray-600">End Date</label>
                <input type="date" id="end_date" wire:model.live="endDate" class="px-4 py-2 border rounded-lg">
            </div>
            <div class="flex items-center space-x-4 mb-4">
                <!-- Filter Button -->
                <button wire:click="filterStockByDate"
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400"
                    wire:loading.attr="disabled">
                    Filter
                </button>

                <!-- Spinner (visible when loading) -->
                <div wire:loading wire:target="filterStockByDate" class="input-group-append">
                    <span class="input-group-text">
                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                    </span>
                </div>

                <!-- Reset Filter Button (X Icon) -->
                @if ($startDate && $endDate)
                    <button wire:click="resetFilter" class="text-red-500 text-xl">
                        <i class="fas fa-times-circle"></i>
                    </button>
                @endif
            </div>


        </div>

        <!-- No Results Message -->
        @if ($stocks->isEmpty() && $startDate && $endDate)
            <div class="bg-yellow-100 text-yellow-700 p-4 rounded-lg mb-4">
                <p>No stocks found for the selected date range:
                    <strong>{{ \Carbon\Carbon::parse($startDate)->format('F j, Y') }} to
                        {{ \Carbon\Carbon::parse($endDate)->format('F j, Y') }}</strong>.
                </p>
            </div>
        @endif


        <!-- Stock Table -->
        <div class="table-responsive">
            @if($stocks->isEmpty()) <!-- Check if no stock is found -->
            <div class="alert alert-warning text-center" role="alert">
                <i class="fas fa-exclamation-circle fa-2x text-warning mb-2"></i>
                <h5 class="mt-2">No Stock found!</h5>
                <p>Click the "Add Stock" button to create your first stock.</p>
            </div>
            @else
                <table class="table min-w-full table-hover table-striped border border-gray-200 text-sm text-gray-700 rounded-lg overflow-hidden">
                    <thead class="bg-gray-300 text-gray-800 font-semibold uppercase">
                        <tr>
                            <th class="px-6 py-3">Product</th>
                            <th class="px-6 py-3">Quantity</th>
                            <th class="px-6 py-3">Available Servings</th>
                            <th class="px-6 py-3">Price Per Unit</th>
                            <th class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stocks as $stock)
                            <tr class="border-b hover:bg-gray-100 transition-colors duration-200">
                                <td class="px-6 py-4">{{ $stock->product->name }}</td>
                                <td class="px-6 py-4">
                                    @if ($stock->quantity == 0)
                                        <span class="badge bg-gray-500 text-white">Out of Stock</span>
                                    @elseif($stock->quantity < $stock->initial_quantity)
                                        <span class="badge bg-red-500 text-white">-{{ $stock->getQuantityDeducted() }} Deduction</span>
                                    @else
                                        <span class="badge bg-green-500 text-white">{{ $stock->quantity }} Available</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">{{ $stock->available_servings }}</td>
                                <td class="px-6 py-4">${{ $stock->price_per_unit }}</td>
                                <td class="px-6 py-4 flex space-x-2">
                                    @if ($stock->quantity === 0)
                                        <button wire:click="editStock({{ $stock->id }})" @click="showForm = true"
                                            class="btn btn-sm btn-outline-primary hover:text-green-500 hover:bg-green-50 transition-all duration-200">
                                            <i class="fas fa-plus mr-1"></i> Add More Stock
                                        </button>
                                    @else
                                        <button wire:click="editStock({{ $stock->id }})" @click="showForm = true"
                                            class="btn btn-sm btn-outline-primary hover:text-blue-500 hover:bg-blue-50 transition-all duration-200">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </button>
                                    @endif
                                    <button wire:click="deleteStock({{ $stock->id }})"
                                        class="btn btn-sm btn-outline-danger hover:text-red-500 hover:bg-red-50 transition-all duration-200">
                                        <i class="fas fa-trash mr-1"></i> Delete
                                    </button>
                                    <button wire:click="recordSale({{ $stock->id }})" @click="showSaleForm = true"
                                        class="btn btn-sm transition-all duration-200"
                                        :class="{
                                            'bg-red-500 text-white': {{ $stock->quantity }} === 0,
                                            'bg-yellow-500 text-white': {{ $stock->quantity }} < {{ $stock->initial_quantity }} && {{ $stock->quantity }} > 0,
                                            'bg-green-500 text-white': {{ $stock->quantity }} >= {{ $stock->initial_quantity }}
                                        }"
                                        :disabled="{{ $stock->quantity === 0 }}">
                                        <i class="fas fa-shopping-cart mr-1"></i>
                                        <span x-text="({{ $stock->quantity }} === 0) ? 'Needs Restocking' : 
                                                  ({{ $stock->quantity }} < {{ $stock->initial_quantity }} && {{ $stock->quantity }} > 0)
                                                  ? 'Record Sale' : 'Record Sale'"></span>
        
                                        @if ($stock->getSalesCount())
                                            <span class="badge bg-green-500 text-white">+{{ $stock->getSalesCount() }} Sales</span>
                                        @endif
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        





    <div class="mb-6 p-4 bg-gray-100 rounded-lg shadow-md" x-show="showSaleForm" x-transition>
        <h3 class="text-2xl font-semibold text-gray-800 mb-4">Record Sale</h3>

        <form wire:submit.prevent="createSale">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Product Selection -->
                @if (!$product)
                    <!-- Only show this if no product is selected -->
                    <div class="mb-4 relative">
                        <label for="saleProduct_id" class="block text-gray-700 font-semibold">
                            Product
                        </label>
                        <select wire:model.live="product_id" id="saleProduct_id"
                            class="p-2 border border-gray-300 rounded-lg w-full" @change="clearProduct">
                            <option value="">Select Product</option>
                            @foreach ($stocks as $stock)
                                <option value="{{ $stock->id }}">{{ $stock->product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <!-- Show product name banner once selected -->
                    <div class="mb-4 relative bg-blue-100 p-4 rounded-lg">
                        <span class="text-xl text-blue-600 font-semibold">Make Sale for:
                            <strong>{{ $product->name }}</strong></span>
                    </div>
                @endif

                <!-- Quantity Sold -->
                <div class="mb-4 relative">
                    <label for="quantitySold" class="block text-gray-700 font-semibold">
                        Quantity Sold
                    </label>
                    <input type="number" wire:model.live="quantity"
                        class="p-2 border border-gray-300 rounded-lg w-full" min="1">
                </div>

                <!-- Sale Price Per Unit -->
                <div class="mb-4 relative">
                    <label for="salePrice" class="block text-gray-700 font-semibold">
                        Sale Price Per Unit
                    </label>
                    <input type="number" wire:model.live="price_per_unit"
                        class="p-2 border border-gray-300 rounded-lg w-full" step="0.01" min="0">
                </div>

                <!-- Total Amount -->
                <div class="mb-4 relative">
                    <label for="totalAmount" class="block text-gray-700 font-semibold">
                        Total Amount
                    </label>
                    <input type="text" wire:model.live="totalAmount" readonly
                        class="p-2 border border-gray-300 rounded-lg w-full bg-gray-200">
                </div>
            </div>

            <div class="flex items-center space-x-4 mt-4">
                <button type="submit"
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    Record Sale
                </button>
                <button type="button" @click="showSaleForm = false; $wire.resetForm()"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg shadow hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
