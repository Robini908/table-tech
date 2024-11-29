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
        <div class="table-responsive">
            <table
                class="table min-w-full table-hover table-striped border border-gray-200 text-sm text-gray-700 rounded-lg overflow-hidden">
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
                                    <!-- Stock is fully depleted -->
                                    <span class="badge bg-gray-500 text-white">Out of Stock</span>
                                @elseif($stock->quantity < $stock->initial_quantity)
                                    <!-- Deductions have started -->
                                    <span class="badge bg-red-500 text-white">-{{ $stock->getQuantityDeducted() }}
                                        Deduction</span>
                                @else
                                    <!-- Enough stock available -->
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
                                        'bg-yellow-500 text-white': {{ $stock->quantity }} <
                                            {{ $stock->initial_quantity }} && {{ $stock->quantity }} > 0,
                                        'bg-green-500 text-white': {{ $stock->quantity }} >=
                                            {{ $stock->initial_quantity }}
                                    }"
                                    :disabled="{{ $stock->quantity === 0 }}">
                                    <i class="fas fa-shopping-cart mr-1"></i>
                                    <!-- Conditional text based on stock status -->
                                    <span
                                        x-text="({{ $stock->quantity }} === 0) ? 'Needs Restocking' : 
                                                  ({{ $stock->quantity }} < {{ $stock->initial_quantity }} && {{ $stock->quantity }} > 0)
? 'Record Sale' : 
                                                  'Record Sale'"></span>

                                    @if ($stock->getSalesCount())
                                        <span class="badge bg-green-500 text-white">+{{ $stock->getSalesCount() }}
                                            Sales</span>
                                    @endif
                                </button>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
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








    <div class="mb-6 p-6 bg-white rounded-lg shadow-md overflow-x-auto">
        <h3 class="text-3xl font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-exclamation-triangle mr-3 text-yellow-500 text-2xl"></i> Excess Demand Logs
        </h3>

        <!-- Informative Message Section -->
        <div class="mb-6 p-4 bg-yellow-50 text-yellow-700 rounded-md shadow-md">
            <i class="fas fa-info-circle mr-2 text-2xl"></i>
            <strong>The system is facing unexpected surges in demand, where sold quantities exceed stock. This
                mismatch may affect order fulfillment. The logs below help identify and resolve issues related to
                inventory, forecasting, and supply chain.</strong>

            <ul class="list-inside list-disc mt-2">
                <li>Stock Data Errors: Outdated or inaccurate inventory data.</li>
                <li>Surprise Spikes in Demand: Unexpected surges due to events or trends.</li>
                <li>Supplier Delays: Late reorders or supply chain disruptions.</li>
                <li>Large Customer Orders: Bulk or urgent requests exceeding stock.</li>
            </ul>

            <div class="mt-4">
                <strong>Next Steps:</strong> Regularly review these logs to adjust stock levels, improve reorder
                processes, and refine demand forecasting to avoid stockouts and enhance efficiency.
            </div>
        </div>

        <!-- Error Message -->
        @if ($errorMessage)
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-md shadow-md flex items-center">
                <i class="fas fa-exclamation-circle mr-2 text-xl"></i> {{ $errorMessage }}
            </div>
        @endif

        <!-- Search and Filter Section -->
        <div class="mb-4 flex justify-between items-center">
            <input type="text" placeholder="Search Logs..."
                class="p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 text-lg"
                wire:model.live="searchTerm">

            <!-- Only enable the download button if logs are available -->
            <button
                class="px-6 py-3 bg-yellow-500 text-white rounded-md shadow-md hover:bg-yellow-600 transition duration-200 disabled:opacity-50"
                wire:click="downloadCSV" @disabled(empty($excessDemandLogs))>
                <i class="fas fa-download mr-2"></i> Download Logs
            </button>
        </div>

        <div class="mb-6 p-6 bg-white rounded-lg shadow-md overflow-x-auto">
            <h3 class="text-3xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-exclamation-triangle mr-3 text-yellow-500 text-2xl"></i> Excess Demand Logs
            </h3>

            <!-- Informative Message Section -->
            <div class="mb-6 p-4 bg-yellow-50 text-yellow-700 rounded-md shadow-md">
                <i class="fas fa-info-circle mr-2 text-2xl"></i>
                <strong>The system is facing unexpected surges in demand...</strong>
                <ul class="list-inside list-disc mt-2">
                    <li>Stock Data Errors: Outdated or inaccurate inventory data.</li>
                    <li>Surprise Spikes in Demand: Unexpected surges due to events or trends.</li>
                    <li>Supplier Delays: Late reorders or supply chain disruptions.</li>
                    <li>Large Customer Orders: Bulk or urgent requests exceeding stock.</li>
                </ul>
            </div>

            <!-- Error Message -->
            @if ($errorMessage)
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-md shadow-md flex items-center">
                    <i class="fas fa-exclamation-circle mr-2 text-xl"></i> {{ $errorMessage }}
                </div>
            @endif

            <!-- Search and Filter Section -->
            <div class="mb-4 flex justify-between items-center">
                <input type="text" placeholder="Search Logs..."
                    class="p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 text-lg"
                    wire:model.live="searchTerm">
                <button
                    class="px-6 py-3 bg-yellow-500 text-white rounded-md shadow-md hover:bg-yellow-600 transition duration-200 disabled:opacity-50"
                    wire:click="downloadCSV" @disabled(empty($excessDemandLogs))>
                    <i class="fas fa-download mr-2"></i> Download Logs
                </button>
            </div>

            <!-- Excess Demand Logs Table with Fixed Height and Scroll -->
            @if (!empty($excessDemandLogs))
                <div class="overflow-y-auto max-h-96">
                    <table class="min-w-full table-auto border-collapse text-sm text-gray-700" wire:poll.1s>
                        <thead>
                            <tr class="bg-gray-100 text-lg">
                                <th class="px-6 py-3 text-left font-medium">Product</th>
                                <th class="px-6 py-3 text-left font-medium">Sold Quantity</th>
                                <th class="px-6 py-3 text-left font-medium">Available Quantity</th>
                                <th class="px-6 py-3 text-left font-medium">Date</th>
                                <th class="px-6 py-3 text-left font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($excessDemandLogs as $log)
                                <tr class="border-b hover:bg-gray-50 transition duration-200">
                                    <td class="px-6 py-4">{{ $log['product'] }}</td>
                                    <td class="px-6 py-4">{{ $log['sold'] }}</td>
                                    <td class="px-6 py-4">{{ $log['available'] }}</td>
                                    <td class="px-6 py-4">
                                        {{ \Carbon\Carbon::parse($log['date'])->format('l, F jS, Y \a\t h:i A') }}</td>
                                    <td class="px-6 py-4 flex items-center">
                                        @if ($log['excessDemand'])
                                            <span class="text-red-600 font-semibold">Excess Demand</span>
                                            <!-- Info icon with tooltip for Excess Demand -->
                                            <i class="fas fa-info-circle text-red-600 ml-2" data-bs-toggle="tooltip"
                                                title="Sold quantity exceeds available stock due to high demand."></i>
                                        @else
                                            <span class="text-green-600 font-semibold">Normal</span>
                                            <!-- Info icon with tooltip for Normal -->
                                            <i class="fas fa-info-circle text-green-600 ml-2" data-bs-toggle="tooltip"
                                                title="Stock levels are within normal range."></i>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-4 bg-gray-100 text-gray-700 rounded-md shadow-md">
                    <i class="fas fa-info-circle mr-2 text-yellow-500"></i> No excess demand logs found.
                </div>
            @endif
        </div>




        <!-- Profit & Loss -->
        <div class="mb-6 p-6 bg-white rounded-lg shadow-md grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
            wire:poll.1s>
            <!-- Bootstrap Alert for Calculation Status -->
            <div class="col-span-1 sm:col-span-2 lg:col-span-3">
                <div class="alert alert-info alert-dismissible fade show p-4 shadow-lg rounded-lg" role="alert">
                    <strong>Profit & Loss Calculation Complete!</strong>
                    <div class="text-sm mt-2">
                        <p>The current profit and loss have been successfully calculated based on the following:</p>
                        <ul class="mt-2 space-y-1">
                            <li><strong>Total Revenue:</strong> The total amount earned from all sales.</li>
                            <li><strong>Total Cost:</strong> The total expenses, including the cost of goods sold (COGS)
                                and operational costs.</li>
                            <li><strong>Profit/Loss:</strong> Calculated as <strong>Total Revenue - Total Cost</strong>.
                            </li>
                        </ul>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>

            <!-- Total Profit/Loss Card -->
            <div class="bg-white p-4 rounded-lg shadow-md flex items-center justify-between">
                <h3 class="text-2xl font-semibold text-gray-800">
                    <i class="fas fa-chart-line mr-3 text-green-500"></i> Profit & Loss (P&L)
                </h3>
                <p class="text-lg font-semibold text-green-600">${{ $profitLoss }}</p>
            </div>

            <!-- Profit Breakdown Card -->
            <div class="bg-white p-4 rounded-lg shadow-md" wire:poll.1s>
                <h4 class="text-xl font-semibold text-gray-800 mb-2">Profit Breakdown by Product</h4>

                <!-- Profit Breakdown List with Fixed Height and Scroll -->
                <div class="max-h-96 overflow-y-auto">
                    <ul class="space-y-2">
                        @foreach ($profitBreakdown as $product)
                            @if (floatval($product['profit']) > 0)
                                <!-- Only show products with profit -->
                                <li class="flex justify-between">
                                    <span class="text-gray-700">{{ $product['name'] }}</span>
                                    <span class="font-semibold text-green-600">${{ $product['profit'] }}</span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>


            <!-- Recent Sales Card -->
            <div class="bg-white p-4 rounded-lg shadow-md" wire:poll.1s>
                <h4 class="text-xl font-semibold text-gray-800 mb-2">Recent Sales</h4>

                <!-- Bootstrap Carousel for Recent Sales -->
                <div id="recentSalesCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <!-- Loop through the first 10 sales -->
                        @foreach ($recentSales as $index => $sale)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <div class="flex justify-between text-sm text-gray-700 p-2">
                                    <span>{{ $sale['product_name'] }}</span>
                                    <span class="font-semibold">${{ $sale['sale_amount'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Carousel controls -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#recentSalesCarousel"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#recentSalesCarousel"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>


            <!-- Profit/Loss Visualization (Bar Chart) Card -->
            <div class="col-span-1 sm:col-span-2 lg:col-span-1 bg-white p-4 rounded-lg shadow-md">
                <h4 class="text-xl font-semibold text-gray-800 mb-2">Profit/Loss Visualization</h4>
                <div id="profitLossChart"></div>
            </div>

            <!-- Monthly Profit Trend Card -->
            <div class="col-span-1 sm:col-span-2 lg:col-span-1 bg-white p-4 rounded-lg shadow-md">
                <h4 class="text-xl font-semibold text-gray-800 mb-2">Monthly Profit Trend</h4>
                <div id="monthlyTrendChart"></div>
            </div>
        </div>



        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Profit/Loss Visualization Bar Chart
                const profitLossChart = new Chart(document.getElementById('profitLossChart'), {
                    type: 'bar',
                    data: {
                        labels: @json(array_column($profitBreakdown, 'name')), // Product names
                        datasets: [{
                            label: 'Profit/Loss per Product',
                            data: @json(array_column($profitBreakdown, 'profit')), // Profit data
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Monthly Profit Trend Line Chart
                const monthlyTrendChart = new Chart(document.getElementById('monthlyTrendChart'), {
                    type: 'line',
                    data: {
                        labels: @json(array_column($monthlyTrend, 'month')), // Month labels
                        datasets: [{
                            label: 'Monthly Profit Trend',
                            data: @json(array_column($monthlyTrend, 'amount')), // Monthly profit data
                            fill: false,
                            borderColor: 'rgba(153, 102, 255, 1)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
        </script>


    </div>
