<div class="container mx-auto p-6">
    <div class="flex justify-between mb-6">
        <h2 class="text-xl font-semibold">Sales Management</h2>
        <button wire:click="createNewSale" class="bg-blue-500 text-white px-4 py-2 rounded-md">New Sale</button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-500 text-white p-3 rounded-md mb-4">
            {{ session('message') }}
        </div>
    @endif

    {{-- Conditional Rendering for Forms --}}
    @if ($isSelling || $isEditingSale)
        <h3>{{ $isEditingSale ? 'Edit Sale' : 'Create Sale' }}</h3>
        <div class="relative">
            <!-- Close Button -->
            <button wire:click="closeForm" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-2">
                &times;
            </button>

            <!-- Form -->
            <form wire:submit.prevent="{{ $isEditingSale ? 'updateSale' : 'storeSale' }}"
                class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Product Field -->
                <div class="mb-4">
                    <label for="product_id" class="block">Product</label>
                    <select wire:model="product_id" id="product_id"
                        class="w-full p-2 border border-gray-300 rounded-md">
                        <option value="">Select Product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Stock Field -->
                <div class="mb-4">
                    <label for="stock_id" class="block">Stock</label>
                    <select wire:model="stock_id" id="stock_id" class="w-full p-2 border border-gray-300 rounded-md">
                        <option value="">Select Stock</option>
                        @foreach ($stocks as $stock)
                            <option value="{{ $stock->id }}">{{ $stock->stock_code }}</option>
                        @endforeach
                    </select>
                    @error('stock_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Quantity Field -->
                <div class="mb-4">
                    <label for="quantity" class="block">Quantity</label>
                    <input type="number" wire:model="quantity" id="quantity"
                        class="w-full p-2 border border-gray-300 rounded-md">
                    @error('quantity')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Price per Unit Field -->
                <div class="mb-4">
                    <label for="price_per_unit" class="block">Price per Unit</label>
                    <input type="number" wire:model="price_per_unit" id="price_per_unit"
                        class="w-full p-2 border border-gray-300 rounded-md">
                    @error('price_per_unit')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Total Price Field -->
                <div class="mb-4">
                    <label for="total_price" class="block">Total Price</label>
                    <input type="number" wire:model="total_price" id="total_price"
                        class="w-full p-2 border border-gray-300 rounded-md" readonly>
                </div>

                <!-- Submit Button and Close Button in the same row -->
                <div class="col-span-2 flex justify-between items-center">
                    <!-- Close Button -->
                    <button wire:click="closeForm" class="bg-red-500 text-white rounded-full p-2">
                        &times;
                    </button>
                    <!-- Submit Button -->
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">
                        {{ $isEditingSale ? 'Update Sale' : 'Create Sale' }}
                    </button>
                </div>
            </form>
        </div>
    @else
        {{-- Sales List Table --}}
        <h3 class="mt-6">Sales List</h3>
        @if ($sales->isEmpty()) <!-- Check if no stock is found -->
            <div class="alert alert-warning text-center" role="alert">
                <i class="fas fa-exclamation-circle fa-2x text-warning mb-2"></i>
                <h5 class="mt-2">No Sales recorded yet!</h5>
            </div>
        @else
            <table class="min-w-full border-collapse table-auto">
                <thead>
                    <tr>
                        <th class="border p-2">Product</th>
                        <th class="border p-2">Stock</th>
                        <th class="border p-2">Quantity</th>
                        <th class="border p-2">Price per Unit</th>
                        <th class="border p-2">Total Price</th>
                        <th class="border p-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sales as $sale)
                        <tr>
                            <td class="border p-2">{{ $sale->product->name }}</td>
                            <td class="border p-2">{{ $sale->stock->stock_code }}</td>
                            <td class="border p-2">{{ $sale->quantity }}</td>
                            <td class="border p-2">{{ number_format($sale->price_per_unit, 2) }}</td>
                            <td class="border p-2">{{ number_format($sale->total_price, 2) }}</td>
                            <td class="border p-2">
                                <button wire:click="editSale({{ $sale->id }})"
                                    class="bg-yellow-500 text-white px-3 py-1 rounded-md">Edit</button>
                                <button wire:click="deleteSale({{ $sale->id }})"
                                    class="bg-red-500 text-white px-3 py-1 rounded-md">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endif
</div>
